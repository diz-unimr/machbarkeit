<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->

	<div v-if="requestStatus === 200" class="ontology-search-tree-container">
		<div :class="['ontology-search-tree-wrapper', { 'ausschlusskriterien-overlay': criteriaType === 'ausschlusskriterien'}]">
			<div class="criteria-type">
				{{ criteriaType }}
			</div>
			<div v-if="modules && modules.length > 0">
				<div class="ontology-search-tree__tabs">
					<div class="ontology-search-tree__tabs-container">
						<button v-for="(module) in modules"
							:key="module.id"
							:class="['ontology-tab', { 'active': activeTab === module.id }]"
							@click="changeTab(module.id)">
							{{ module.name }}
						</button>
					</div>
				</div>
				<div v-if="ontologyTree && !isLoading" class="ontology-search-tree__display">
					<div v-if="ontologyTree.length > 0 && ontologyTree[0].moduleId === activeTab">
						<div class="ontology-search-tree__body">
							<div class="module-name">
								{{ modules.filter(module => module.id === activeTab)[0].name }}
							</div>

							<template v-if="searchInputText.length <= 0">
								<OntologyNestedTreeNode v-for="criterion in ontologyTree"
									:key="criterion.id"
									:criterion="criterion" />
							</template>

							<template v-else>
								<OntologyNestedTreeNodeSearchInput v-for="criterion in ontologyTree"
									:key="criterion.id"
									class="ontology-tree-node"
									:criterion="criterion"
									:search-input-text="searchInputText" />
							</template>
						</div>
					</div>
					<div v-else-if="ontologyTree?.length === 0">
						<div class="no-result-data">
							Keine Daten
						</div>
					</div>
				</div>
				<div v-if="isLoading" class="loading-text ontology-search-tree__display">
					Loading...
				</div>
				<div class="ontology-search-tree__button-group">
					<button :disabled="isCheckboxEmpty" @click="submit(criteriaType)">
						AUSWÄHLEN
					</button>
					<button @click="cancel(criteriaType)">
						ABBRECHEN
					</button>
				</div>
				<WarningModal v-if="!isCheckboxEmpty && isModalOpened"
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
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'
import OntologyNestedTreeNodeSearchInput from './OntologyNestedTreeNodeSearchInput.vue'
import WarningModal from './WarningModal.vue'
import type { OntologySearchTreeModalData, Module, Criterion } from '../types/OntologySearchTreeModalData.ts'
import lodash from 'lodash'
import axios, { AxiosError, type AxiosResponse } from 'axios'

export default Vue.extend({
	name: 'OntologySearchTreeModal',
	components: {
		OntologyNestedTreeNode,
		OntologyNestedTreeNodeSearchInput,
		WarningModal,
	},
	props: {
		criteriaType: {
			type: String,
			default: '',
		},
		searchInputText: {
			type: String,
			default: null,
		},
		getSelectedCriteria: {
			type: Function,
			default: () => {},
		},
	},

	data(): OntologySearchTreeModalData {
		return {
			activeTab: undefined,
			requestStatus: undefined,
			selectedItems: [],
			checkedItems: undefined,
			isSearchResultNoData: [],
			modules: null,
			ontologyTree: null,
			ontologyTreeSearch: [],
			context: null,
			isCheckboxChecked: false,
			isModalOpened: false,
			activeModule: undefined,
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
				if (newVal.length > 1) {
					this.ontologyTree = await this.getOntology(this.activeTab!, this.searchInputText)
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	async created() {
		// get Modules and set activeTab
		this.getModules()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		changeTab(moduleId: string): void {
			this.activeModule = this.modules?.filter(module => module.id === moduleId)[0]
			if (this.activeTab !== undefined && this.activeTab !== moduleId && !this.isCheckboxEmpty) {
				this.isModalOpened = true
			} else this.activateTab(moduleId)
		},

		activateTab(moduleId: string): void {
			this.activeTab = moduleId
			if (this.searchInputText === '') {
				this.getOntology(moduleId)
			} else if (this.searchInputText.length > 1) {
				this.getOntology(moduleId, this.searchInputText)
			}
		},

		async getModules(): Promise<void> {
			try {
				const apiResponse = (await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/modules'))
				// Convert object keys to camelCase using lodash
				const response: Module[] = apiResponse.data.map(obj =>
					lodash.mapKeys(obj, (value, key) => lodash.camelCase(key)),
				)
				this.modules = response
				this.activateTab(response[0].id)
				this.$emit('update-modules', this.modules)
				// localStorage.setItem('moduleName', JSON.stringify(this.modules))
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
			}
		},

		async getOntology(moduleId: string, searchText: string = '_null_'): Promise<Criterion[] | null> {
			this.isLoading = true
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
						},
					)
				} else {
					apiResponse = await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/tree/' + moduleId)
				}
				// Convert object keys to camelCase using lodash
				const response = transformObjectKeys(apiResponse.data)

				this.ontologyTree = response
				this.ontologyTreeSearch[moduleId] = response || [] // problem mit moduleId und index

				this.requestStatus = apiResponse.status as number
				this.$emit('get-request-status', this.requestStatus)
				this.isLoading = false
				return this.ontologyTree
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
				this.requestStatus = (error as AxiosError).status as number
				this.$emit('get-request-status', this.requestStatus, (error as AxiosError).message)
				this.isLoading = false
				return null
			}
		},

		getCheckedItems(items: Criterion[], checkedItemsId: string[], checkedItems: Criterion[]): Criterion[] {
			// Clear once at start of root call
			this.selectedItems = []

			const traverse = (items: Criterion[]) => {
				for (const item of items) {
					// Early exit if we already found everything
					if (this.selectedItems.length === checkedItems!.length) {
						return
					} else if (item.selectable && checkedItemsId.includes(item.id)) {
						this.selectedItems.push(item)

						// Optional: remove children from checkedItems based on module logic
						if (item.children && item.children.length > 0) {
							const codePrefix = item.termCodes[0].code.slice(0, 3)
							if (this.activeModule?.name === 'Diagnose' || this.activeModule?.name === 'Prozedur') {
								checkedItems = checkedItems!.filter(checkedItem =>
									checkedItem.id === item.id || !checkedItem.termCodes[0].code.startsWith(codePrefix))
							} else if (this.activeModule?.name === 'Laboruntersuchung') {
								checkedItems = checkedItems!.filter(checkedItem =>
									checkedItem.id === item.id || !checkedItem.termCodes[0].code.startsWith(codePrefix))
							}
						}
					} else if (item.children && item.children.length > 0) {
						traverse(item.children)
					}
				}
			}

			traverse(items)
			return this.selectedItems
		},

		submit(criteriaType: string): void {
			const checkedItems = [...Array.from(this.$store.state.checkedItemsMap.values())] as Criterion[]
			const checkedItemsId = checkedItems!.map(checkedItem => checkedItem.id)
			const selectedItems = this.getCheckedItems(this.ontologyTree!, checkedItemsId, checkedItems)
			this.$emit('get-selected-criteria', criteriaType, selectedItems)
			this.$store.dispatch('clearCheckedItem')
		},

		cancel(criteriaType: string): void {
			this.$emit('toggle-ontology-search-tree-modal', criteriaType)
			this.$store.dispatch('clearCheckedItem')
		},

		submitChangeTab(): void {
			this.isModalOpened = false
			this.activateTab(this.activeModule!.id)
			this.$store.dispatch('clearCheckedItem')
		},

		cancelChangeTab(): void {
			this.isModalOpened = false
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
	width: 95%;
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
	border: 1px solid #004cff;
}

.ontology-search-tree__tabs-container button:active {
	background-color: #738cba;
	color: #ffffff;
}

.ontology-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
}

.ontology-tab.active {
	background-color: #738cba;
	color: white;
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
