<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-search-tree-container">
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
							@click="activeTab = module.id">
							{{ module.moduleName }}
						</button>
					</div>
				</div>

				<div v-if="ontologyTree && ontologyTree.length > 0 && ontologyTree[0].moduleId === activeTab" class="ontology-search-tree__display">
					<div class="ontology-search-tree__body">
						<div class="module_name">
							{{ modules.filter(module => module.id === activeTab)[0].display }}
						</div>
						<template v-if="searchInputText.length <= 0">
							<!-- :module-info="activeModule" -->
							<OntologyNestedTreeNode v-for="criterion in ontologyTree"
								:key="criterion.id"
								:criterion="criterion"
								@input="getCheckboxItems" />
						</template>

						<template v-else>
							<OntologyNestedTreeNodeSearchInput v-for="criterion in ontologyTree"
								:key="criterion.id"
								class="ontology-tree-node"
								:criterion="criterion"
								:search-input-text="searchInputText"
								@input="getCheckboxItems" />
						</template>
					</div>
					<div class="ontology-search-tree__button-group">
						<button :disabled="selectedItems.length > 0 ? false : true" @click="submit(criteriaType, selectedItems)">
							AUSWÄHLEN
						</button>
						<button @click="cancel(criteriaType)">
							ABBRECHEN
						</button>
					</div>
				</div>
				<div v-else-if="ontologyTree?.length === 0" class="no-result-data">
					Keine Daten
				</div>
				<div v-else class="loading-text">
					Loading...
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import nextcloudAxios from '@nextcloud/axios'
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'
import OntologyNestedTreeNodeSearchInput from './OntologyNestedTreeNodeSearchInput.vue'
import type { OntologySearchTreeModalData, Modules, OntologyTreeElement } from '../types/OntologySearchTreeModalData.ts'
import type { CheckedItem } from './OntologyNestedTreeNode.vue'

export default Vue.extend({
	name: 'OntologySearchTreeModal',
	components: {
		OntologyNestedTreeNode,
		OntologyNestedTreeNodeSearchInput,
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
			activeTab: 1,
			wasTabClicked: [],
			ontologyResponse: null,
			selectedItems: [],
			isSearchResultNoData: [],
			modules: null,
			ontologyTree: null,
			ontologyTreeSearch: [],
			hashId: [],
		}
	},

	watch: {
		searchInputText: {
			async handler() {
				if (this.searchInputText.length > 0) {
					localStorage.setItem('ontologySearch', JSON.stringify(null))
					this.ontologyTree = await this.getOntology(this.activeTab!, this.searchInputText)
				}
			},
			immediate: true,
		},

		activeTab: {
			async handler() {
				await this.getOntology(this.activeTab!, this.searchInputText)
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	async created() {
		/* const param = [1, 3]
		const response = await nextcloudAxios.get(generateUrl('/apps/machbarkeit/machbarkeit/filters/[' + param + ']'))
		console.log(response) */
		if (JSON.parse(localStorage.getItem('moduleName')!)) {
			this.modules = JSON.parse(localStorage.getItem('moduleName')!)
		} else await this.getModules()

		if (this.searchInputText.length <= 0) {
			await this.getOntology(1)
		}
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getModules(): Promise<void> {
			try {
				const response: Modules[] = (await nextcloudAxios.get(generateUrl('/apps/machbarkeit/machbarkeit/modules'))).data
				this.modules = response
				localStorage.setItem('moduleName', JSON.stringify(this.modules))
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
			}
		},

		async getOntology(moduleId: number, searchText: string | null = null): Promise<OntologyTreeElement[] | null> {
			// Code oder Suchbegriff eingegeben wurde
			if (searchText) {
				const ontologySearch = JSON.parse(localStorage.getItem('ontologySearch')!)
				if (ontologySearch && ontologySearch[moduleId]) {
					this.ontologyTree = ontologySearch[moduleId]
				} else {
					const response = await nextcloudAxios.get(generateUrl('/apps/machbarkeit/machbarkeit/search_ontology/' + searchText + '/' + moduleId))
					this.ontologyTree = response ? response.data : []
					this.ontologyTreeSearch[moduleId] = response ? response.data : []
					localStorage.setItem('ontologySearch', JSON.stringify(this.ontologyTreeSearch))
				}
				return this.ontologyTree
			} else {
				this.ontologyTree = JSON.parse(localStorage.getItem('ontology:' + moduleId)!) ?? (
					async () => {
						try {
							const response = await nextcloudAxios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology/' + moduleId))
							this.ontologyTree = response.data
							for (let i = 0; i < response.data.length; i++) {
								this.ontologyTree![i].termCodes = JSON.parse(response.data[i].termCodes)
							}
							localStorage.setItem('ontology:' + moduleId, JSON.stringify(response.data))
							return this.ontologyTree
						} catch (error) {
						// eslint-disable-next-line no-console
							console.log((error as Error).message)
							return []
						}
					}
				)()
			}
			return this.ontologyTree
		},

		getCheckboxItems(checkedItem: CheckedItem): void {
			if (checkedItem.action === 'check') {
				this.selectedItems = [...this.selectedItems, checkedItem.node]
			} else if (checkedItem.action === 'uncheck') {
				this.selectedItems = this.selectedItems.filter(name => {
					return name !== checkedItem.node
				})
			}
		},

		submit(criteriaType: string, selectedItems: OntologyTreeElement[]): void {
			this.$emit('get-selected-criteria', criteriaType, selectedItems)
			this.$store.dispatch('clearCheckedItem')
		},

		cancel(criteriaType: string): void {
			this.$emit('toggle-ontology-search-tree-modal', criteriaType)
			this.$store.dispatch('clearCheckedItem')
		},
	},
})
</script>

<style scoped>
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
	/* margin-top: 20px; */
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
	overflow-y: auto;
	margin: 30px 25px 20px 30px;
}

.module_name {
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
</style>
