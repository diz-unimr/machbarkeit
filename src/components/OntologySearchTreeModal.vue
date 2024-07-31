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
			<div v-if="ontologyResponse && ontologyResponse.length > 0">
				<div class="ontology-search-tree__tabs">
					<div class="ontology-search-tree__tabs-container">
						<div v-for="(modulName, modulName_index) in ontologyResponse.map((item) => item.display)"
							:key="modulName_index"
							:class="['ontology-tab', { 'active': activeTab === modulName_index }]"
							@click="activateTab(modulName_index)">
							{{ modulName }}
						</div>
					</div>
				</div>
				<div class="ontology-search-tree__display">
					<div v-for="(criterion, criterionIndex) in ontologyResponse"
						v-show="activeTab === criterionIndex"
						:key="criterionIndex">
						<div class="ontology-search-tree__body">
							<div v-if="isSearchResultNoData[criterionIndex] && (inclusionSearchInput.length > 0 || exclusionSearchInput.length > 0)" class="no-result-data">
								Keine Daten
							</div>

							<OntologyNestedTreeNode v-if="criterion.children && inclusionSearchInput.length <= 0 && exclusionSearchInput.length <= 0"
								:is-root-node="true"
								:criterion="criterion"
								@input="getCheckboxItems" />

							<OntologyNestedTreeNodeSearchInput v-if="criterion.children && (inclusionSearchInput.length > 0 || exclusionSearchInput.length > 0)"
								class="ontology-nested-tree-node"
								:criterion="criterion"
								:index="criterionIndex"
								:inclusion-search-input="inclusionSearchInput"
								:exclusion-search-input="exclusionSearchInput"
								@input="getCheckboxItems"
								@check-existing-data="checkExistingData" />
						</div>
					</div>
					<div class="ontology-search-tree__button-group">
						<button :disabled="selectedItems.length > 0 ? false : true" @click="$emit('get-selected-criteria', criteriaType, selectedItems)">
							AUSWÄHLEN
						</button>
						<button @click="$emit('toggle-ontology-search-tree-modal', criteriaType)">
							ABBRECHEN
						</button>
					</div>
				</div>
			</div>
			<div v-else class="loading-text">
				Loading...
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'
import OntologyNestedTreeNodeSearchInput from './OntologyNestedTreeNodeSearchInput.vue'
import type { OntologySearchTreeModalData } from '../types/OntologySearchTreeModalData.ts'
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
		inclusionSearchInput: {
			type: String,
			default: null,
		},
		exclusionSearchInput: {
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
			activeTab: 0,
			ontologyResponse: null,
			selectedItems: [],
			isSearchResultNoData: [],
		}
	},

	watch: {
		inclusionSearchInput() {
			this.inclusionSearchInput.length > 0 && (this.isSearchResultNoData = Array(this.ontologyResponse?.length).fill(true))
		},

		exclusionSearchInput() {
			this.exclusionSearchInput.length > 0 && (this.isSearchResultNoData = Array(this.ontologyResponse?.length).fill(true))
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getOntology()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getOntology(): Promise<void> {
			const jsonData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology'))
			this.ontologyResponse = jsonData.data

			if (this.inclusionSearchInput.length > 0 || this.exclusionSearchInput.length > 0) {
				this.isSearchResultNoData = Array(this.ontologyResponse?.length).fill(true)
			}
		},

		activateTab(index: string | number): void {
			this.activeTab = index
		},

		getCheckboxItems(checkedItem: CheckedItem): void {
			if (checkedItem.action === 'check') {
				this.selectedItems = [...this.selectedItems, checkedItem.node]
			} else if (checkedItem.action === 'uncheck') {
				this.selectedItems = this.selectedItems.filter(function(name) {
					return name !== checkedItem.node
				})
			}
		},

		checkExistingData(index: number): void {
			this.isSearchResultNoData.splice(index, 1, false)
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

.ontology-nested-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding: 0px 10px;
	margin-top: 20px;
}

.ontology-search-tree-wrapper {
	display: flex;
	flex-direction: column;
	position: absolute;
	min-height: 12em;
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

.ontology-search-tree__tabs-container div {
	background-color: #ffffff;
	color: black;
	border: 1px solid #2e4884;
	border-radius: 5px;
	padding: 0.4em 0.5em;
	margin: 0.4em 0.5em;
	text-align: center;
	cursor: pointer;
	text-wrap: nowrap;
	min-width: 70px;
}

.ontology-search-tree__tabs-container div:active {
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
	height: 650px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.ontology-search-tree__body {
	height: 570px;
	padding: 20px 25px 20px 25px;
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
	font-weight: bold;
	font-size: large;
	margin-top: 20px;
}
</style>
