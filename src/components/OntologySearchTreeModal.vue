<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="search-tree-overlay-container">
		<div class="search-tree-overlay-wrapper" :class="{ 'ausschlusskriterien-overlay': criteriaType === 'Ausschlusskriterien' }">
			<div class="criteria-name">
				{{ criteriaType }}
			</div>
			<div v-if="criteriaResponse && criteriaResponse.length > 0">
				<div class="criteria-search-tree-header">
					<div class="search-tree-button">
						<div v-for="(modulName, modulName_index) in criteriaResponse.map((item) => item.display)"
							:key="modulName_index"
							class="criteria-tab"
							:class="{ 'active': activeTab === modulName_index }"
							@click="activateTab(modulName_index)">
							{{ modulName }}
						</div>
					</div>
				</div>
				<div class="criteria-container">
					<div v-for="(criterion, criterionIndex) in criteriaResponse"
						v-show="activeTab === criterionIndex"
						:key="criterionIndex">
						<div class="criteria-node">
							{{ isNoData }}
							<div v-if="isNoData && !isOntologyButtonClicked" class="noData">
								Kein Daten
							</div>
							<!-- isOntologyButtonClicked -->
							<OntologyNestedTreeNode v-if="criterion.children && einschlussTextSerach.length <= 0"
								:is-root-node="true"
								:criterion="criterion"
								@input="getCheckboxItems" />

							<OntologyNestedTreeNodeSearchInput v-if="criterion.children && (einschlussTextSerach.length > 0 || ausschlussTextSerach.length > 0)"
								class="criteria-nested-tree-node"
								:criterion="criterion"
								:einschluss-text-serach="einschlussTextSerach"
								:ausschluss-text-serach="ausschlussTextSerach"
								@input="getCheckboxItems"
								@check-no-data="checkNoData" />
						</div>
					</div>
					<div class="button-group">
						<button :disabled="selectedItems.length > 0 ? false : true" @click="$emit('get-selected-criteria', {criteriaType, selectedItems})">
							AUSWÄHLEN
						</button>
						<button @click="$emit('toggle-search-criteria-modal', criteriaType)">
							ABBRECHEN
						</button>
					</div>
				</div>
			</div>
			<div v-else class="criteria-loading">
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
import type { OntologySearchTreeModalData, OntologyTreeElement } from '../types/OntologySearchTreeModalData.ts'
import type { CheckedItem } from '../components/OntologyNestedTreeNode.vue'

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
		einschlussTextSerach: {
			type: String,
			default: null,
		},
		ausschlussTextSerach: {
			type: String,
			default: null,
		},
		isOntologyButtonClicked: {
			type: Boolean,
			default: false,
		},
		getSelectedCriteria: {
			type: Function,
			default: () => {},
		},
		toggleSearchCriteriaModal: {
			type: Function,
			default: () => {},
		},
	},

	data(): OntologySearchTreeModalData {
		return {
			activeTab: 0,
			criteriaResponse: null,
			criteriaData: null,
			selectedItems: [],
			isNoData: true,
		}
	},

	watch: {},

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
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		async getOntology(): Promise<void> {
			const jsonData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology'))
			this.criteriaResponse = jsonData.data
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

		checkNoData(filteredCriteria: OntologyTreeElement | null): void {
			filteredCriteria ? (this.isNoData = false) : (this.isNoData = true)
		},
	},
})
</script>

<style scoped>
.search-tree-overlay-container {
	display:flex;
	z-index: 100;
	max-width: 100%;
	max-height: 100%;
	position: sticky;
}

.criteria-nested-tree-node {
	overflow-y: auto;
	overflow-x: hidden;
	scrollbar-width: auto;
	height: 100%;
	padding-right: 10px;
}

.search-tree-overlay-wrapper {
	display: flex;
	flex-direction: column;
	position: absolute;
	min-height: 12em;
	background-color: #fff;
	box-shadow: 0px 10px 15px 0px #0003, 0px 0px 25px 2px #00000024, 0px 0px 10px 0px #0000001f;
	pointer-events: auto;
	width: 95%;
}

.criteria-name {
	text-align: center;
	margin: 5px 0px;
	font-size: 18px;
	font-weight: bold;
}

.ausschlusskriterien-overlay {
	right: 0;
}

.criteria-search-tree-header {
	display: flex;
	flex-direction: row;
	place-content: stretch flex-start;
	align-items: stretch;
	border-top: 2px solid #adbcd7;
	border-bottom: 2px solid #adbcd7;
	overflow-x: auto;
}

.search-tree-button {
	display: flex;
	margin: auto;
	padding: 0px 30px;
}

.search-tree-button div {
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

.search-tree-button div:active {
	background-color: #738cba;
	color: #ffffff;
}

.criteria-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
}

.criteria-tab.active {
	background-color: #738cba;
	color: white;
}

.criteria-container {
	height: 650px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.criteria-node {
	height: 570px;
	padding: 20px 25px 20px 25px;
}

.button-group {
	display: flex;
	column-gap: 15px;
	margin: 20px;
	justify-content: flex-end;
	flex-direction: row;
}

.button-group button {
	border-radius: 8px;
}

.criteria-loading {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 150px;
	border-top: 2px solid #adbcd7;
}

.noData {
	text-align: center;
	font-weight: bold;
	font-size: large;
	margin-top: 20px;
}
</style>
