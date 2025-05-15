<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->

	<div v-if="requestStatus === '200'" class="ontology-search-tree-container">
		<div class="ontology-search-tree-wrapper">
			<div class="criteria-type">
				{{ criteriaType }}
			</div>
			<div v-if="modules && modules.length > 0">
				<div class="ontology-search-tree__tabs">
					<div class="ontology-search-tree__tabs-container">
						<button v-for="module in modules"
							:key="module.id"
							:class="['ontology-tab', { 'active': currentModule?.id === module.id }]"
							@click="handleTab(module)">
							{{ module.name }}
						</button>
					</div>
				</div>
				<div v-if="isLoading" class="loading-text ontology-search-tree__display">
					Loading...
				</div>
				<div v-else-if="!isLoading && (!ontologyTree || ontologyTree?.length === 0)">
					<div class="no-result-data">
						Keine Daten
					</div>
				</div>
				<div v-else-if="!isLoading && (ontologyTree && ontologyTree.length > 0)" class="ontology-search-tree__display">
					<div class="ontology-search-tree__body">
						<div class="module-name">
							{{ modules.filter(module => module.id === currentModule?.id)[0].name }}
						</div>
						<OntologyTreeNode v-for="criterion in ontologyTree"
							:key="criterion.id"
							:module-name="modules.filter(module => module.id === currentModule?.id)[0].name"
							:criterion="criterion" />
					</div>
				</div>
				<div class="ontology-search-tree__button-group">
					<button :disabled="isCheckboxEmpty" @click="submit(criteriaType)">
						AUSWÄHLEN
					</button>
					<button @click="cancel(criteriaType)">
						ABBRECHEN
					</button>
				</div>
				<WarningModal v-if="!isCheckboxEmpty && isWarningModalOpened"
					class="middel"
					@submit-change-tab="submitChangeTab"
					@cancel-change-tab="cancelChangeTab" />
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import transformObjectKeys from '../utils/transformObjectKeys'
import OntologyTreeNode from './OntologyTreeNode.vue'
import WarningModal from './WarningModal.vue'
import type { OntologySearchTreeModalData, Module, Criterion } from '../types/OntologySearchTreeModalData.ts'
import lodash from 'lodash'
import axios, { AxiosError, type AxiosResponse } from 'axios'

export default Vue.extend({
	name: 'OntologySearchTreeModal',
	components: {
		OntologyTreeNode,
		WarningModal,
	},
	props: {
		criteriaType: {
			type: String,
			default: '',
		},
		searchInputText: {
			type: String,
			default: undefined,
		},
		getSelectedCriteria: {
			type: Function,
			default: () => {},
		},
	},

	data(): OntologySearchTreeModalData {
		return {
			currentModule: undefined,
			nextModule: undefined,
			requestStatus: undefined,
			selectedItems: [],
			checkedItems: undefined,
			isSearchResultNoData: [],
			modules: null,
			ontologyTree: null,
			context: null,
			isCheckboxChecked: false,
			isWarningModalOpened: false,
			abortController: undefined,
			isLoading: true,
		}
	},

	computed: {
		isCheckboxEmpty() {
			return this.$store.state.checkedItemsMap.size < 1
		},
	},

	watch: {
		searchInputText: {
			async handler(newVal: string) {
				if (newVal.length > 1 || newVal.length === 0) {
					if (this.ontologyTree) {
						this.isWarningModalOpened = true
					} else this.ontologyTree = await this.getOntology(this.currentModule!.id, this.searchInputText)
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	async created() {
		// get Modules and set currentModule
		this.getModules()
		// this.currentModule = modules[0]
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		handleTab(module: Module): void {
			this.nextModule = module
			if (this.currentModule?.id !== this.nextModule.id && !this.isCheckboxEmpty) {
				this.isWarningModalOpened = true
			} else if (this.nextModule.id !== this.currentModule?.id && this.isCheckboxEmpty) {
				this.activateTab(this.nextModule)
			}
		},

		async activateTab(module: Module): Promise<void> {
			this.currentModule = module
			if (this.searchInputText === '' || this.searchInputText === undefined) {
				this.ontologyTree = await this.getOntology(module.id)
			} else if (this.searchInputText.length > 1) {
				this.ontologyTree = await this.getOntology(module.id, this.searchInputText)
			}
		},

		async getModules(): Promise<Module[] | null> {
			try {
				const apiResponse = (await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/modules'))
				// Convert object keys to camelCase using lodash
				const response: Module[] = apiResponse.data.map(obj =>
					lodash.mapKeys(obj, (value, key) => lodash.camelCase(key)),
				)
				this.modules = response
				this.currentModule = this.modules[0]
				this.handleTab(response[0])
				this.$emit('update-modules', this.modules)
				// localStorage.setItem('moduleName', JSON.stringify(this.modules))
				return this.modules
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
				alert((error as Error).message)
				this.$emit('get-request-status', (error as Error).message)
				return null
			}
		},

		async getOntology(moduleId: string, searchText: string = '_null_'): Promise<Criterion[] | null> {
			this.isLoading = true
			this.ontologyTree = null
			// Cancel previous request if exists
			if (this.abortController) {
				this.abortController.abort()
			}
			this.abortController = new AbortController()

			// Code oder Suchbegriff wurde eingegeben
			let apiResponse: AxiosResponse
			try {
				if (searchText.length > 0 && searchText !== '_null_') {
					apiResponse = await axios.post('https://mdr.diz.uni-marburg.de/api/ontology/concepts/search',
						{
							module_id: moduleId,
							search_term: searchText,
						},
						{
							headers: {
								'Content-Type': 'application/json',
							},
							signal: this.abortController.signal,
						},
					)
				} else {
					apiResponse = await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/tree/' + moduleId, { signal: this.abortController.signal })
				}
				// Convert object keys to camelCase using lodash
				const response = transformObjectKeys(apiResponse.data)
				// this.ontologyTree = response

				this.requestStatus = apiResponse.status!.toString()
				this.$emit('get-request-status', this.requestStatus)
				this.isLoading = false
				return response
			} catch (error) {
				if ((error as AxiosError).code === 'ERR_CANCELED') {
					return null
				} else {
					if ((error as AxiosError).code === 'ERR_NETWORK') {
						alert('Network Error')
					} else {
						this.requestStatus = (error as AxiosError).status!.toString()
						this.$emit('get-request-status', this.requestStatus, (error as AxiosError).message)
					}
					this.isLoading = false
				}
				return null
			}
		},

		activateWarningModal(): boolean {
			this.isWarningModalOpened = true
			return this.isWarningModalOpened
		},

		getCheckedItems(ontologyTree: Criterion[], checkedItemsMap: Map<string, Criterion>, currentModuleName: string): Criterion[] {
			// Clear once at start of root call
			this.selectedItems = []
			const traverse = (ontologyTree: Criterion[]) => {
				for (const item of ontologyTree) {
					// Early exit if we already found everything
					if (this.selectedItems.length === checkedItemsMap!.size) {
						return
					} else if (item.selectable && checkedItemsMap.has(item.id)) {
						this.selectedItems.push(item)
						// remove children from checkedItems based on parent (Diagnose, Prozedur and Laboruntersuchung)
						if (item.children && item.children.length > 0) {
							if (currentModuleName === 'Laboruntersuchung') {
								checkedItemsMap = new Map([...checkedItemsMap!].filter(([id, checkedItem]) =>
									id === item.id || checkedItem.termCodes[0] !== item.termCodes[0]))
							} else {
								const codePrefix = item.termCodes[0].code.slice(0, 3)
								checkedItemsMap = new Map([...checkedItemsMap!].filter(([id, checkedItem]) =>
									id === item.id || !checkedItem.termCodes[0].code.startsWith(codePrefix)))
							}
						}
					} else if (item.children && item.children.length > 0) {
						traverse(item.children)
					}
				}
			}

			traverse(ontologyTree)
			return this.selectedItems
		},

		submit(criteriaType: string): void {
			const checkedItems = this.$store.state.checkedItemsMap
			const selectedItems = this.getCheckedItems(this.ontologyTree!, checkedItems, this.currentModule!.fdpgCdsCode)
			this.$emit('get-selected-criteria', criteriaType, selectedItems)
			this.$store.dispatch('clearCheckedItem')
		},

		cancel(criteriaType: string): void {
			this.$emit('toggle-ontology-search-tree-modal', criteriaType)
			this.$store.dispatch('clearCheckedItem')
		},

		submitChangeTab(): void {
			this.isWarningModalOpened = false
			this.activateTab(this.nextModule!)
			this.$store.dispatch('clearCheckedItem')
		},

		cancelChangeTab(): void {
			this.isWarningModalOpened = false
		},
	},
})
</script>

<style scoped>
.middel {
	position: absolute;
	top: 40%;
	left: 50%;
	transform: translate(-50%, -50%);
}

.ontology-search-tree-container {
	display:flex;
	z-index: 100;
	max-width: 100%;
	max-height: 100%;
	position: relative;
}

.ontology-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding: 0px 10px;
}

.ontology-search-tree-wrapper {
	display: flex;
	flex-direction: column;
	position: absolute;
	min-height: 190px;
	background-color: #fff;
	box-shadow: 0px 10px 15px 0px #0003, 0px 0px 25px 2px #00000024, 0px 0px 10px 0px #0000001f;
	pointer-events: auto;
	width: 100%;
}

.criteria-type {
	text-align: center;
	margin: 10px 0px;
	font-size: 18px;
	font-weight: bold;
	text-transform: capitalize;
}

.ausschlusskriterien-overlay {
	right: 0;
}

.ontology-search-tree__tabs {
	display: flex;
	flex-direction: row;
	place-content: stretch flex-start;
	align-items: stretch;
	border-top: 2px solid #adbcd7;
	border-bottom: 2px solid #adbcd7;
	overflow-x: auto;
	padding: 3px 0px;
}

.ontology-search-tree__tabs-container {
	display: flex;
	margin: auto;
	padding: 0px 30px;
}

.ontology-search-tree__tabs-container button {
	background-color: #ffffff;
	color: black;
	border: 1px solid #2e4884;
	border-radius: 5px;
	padding: 8px;
	margin: 10px;
	text-align: center;
	font-weight: normal;
	cursor: pointer;
	text-wrap: nowrap;
	min-width: 70px;
}

.ontology-search-tree__tabs-container button:hover {
	border: 1px solid #738cba !important;
}

.ontology-search-tree__tabs-container button:active {
	background-color: #738cba;
	border-color: #2e4884;
	color: #ffffff;
}

.ontology-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
}

.ontology-tab.active {
	background-color: #738cba;
	color: #ffffff;
}

.ontology-search-tree__display {
	min-height: 450px;
	max-height: 650px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.ontology-search-tree__body {
	max-height: 620px;
	overflow-y: auto;
	margin: 30px 25px 20px 30px;
}

.module-name {
	font-size: 18px;
	font-weight: bold;
	margin-bottom: 20px;
}

.ontology-search-tree__button-group {
	display: flex;
	column-gap: 15px;
	margin: 20px;
	justify-content: flex-end;
	flex-direction: row;
}

.ontology-search-tree__button-group button {
	border-radius: 8px;
}

.loading-text {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 150px;
	border-top: 2px solid #adbcd7;
}

.no-result-data {
	text-align: center;
	font-size: large;
	margin: 100px;
}

.modal__content {
	margin: 50px;
	text-align: center;
}
</style>
