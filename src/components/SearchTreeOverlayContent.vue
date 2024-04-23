<!-- eslint-disable vue/valid-v-show -->
<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="search-tree-overlay-container">
		<div class="search-tree-overlay-wrapper" :class="{ 'ausschlusskriterien-overlay': criteriaId === 'Ausschlusskriterien' }">
			<div class="criteria-name">
				{{ criteriaId }}
			</div>
			<div v-if="responseArray.length > 0">
				<div class="ontology-search-tree-header">
					<div class="search-tree-button">
						<div v-for="(modulName, modulName_index) in responseArray.map((item) => item.display)"
							:key="modulName_index"
							class="ontology-tab"
							:class="{ 'active': activeTab === modulName_index }"
							@click="activateTab(modulName_index)">
							{{ modulName }}
						</div>
					</div>
				</div>
				<div class="ontology-container">
					<div v-for="(ontology, ontology_index) in responseArray"
						v-show="activeTab === ontology_index"
						:key="ontology_index">
						<div class="ontologyNode">
							<OntologyNestedTreeNode v-if="ontology.children"
								:is-root-node="true"
								:ontology="ontology"
								@input="getCheckboxItems" />
						</div>
					</div>
					<div class="ontology-button">
						<button :disabled="checkedItems.length > 0 ? false : true" @click="showSelectionOntologyDialog({criteriaId: criteriaId, items:checkedItems})">
							AUSWÄHLEN
						</button>
						<button @click="$emit('toggle-search-criteria', criteriaId)">
							ABBRECHEN
						</button>
					</div>
				</div>
			</div>
			<div v-else class="ontology-loading">
				Loading...
			</div>
		</div>
	</div>
</template>

<script>
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { ref } from 'vue'

export default {
	name: 'SearchTreeOverlayContent',
	components: {
		OntologyNestedTreeNode,
	},
	props: {
		isAusschlusskriterien: {
			type: Boolean,
			default: false,
		},
		criteriaId: {
			type: String,
			default: '',
		},
		toggleSearchCriteria: {
			type: Function,
			default: () => {},
		},

	},
	data() {
		return {
			activeTab: 0,
			ontologyModulName: [],
			responseArray: [],
			checkedItems: [],
			state: ref(true),
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	async beforeCreate() {
		const jsonData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology'))
		this.responseArray = jsonData.data
	},
	// Call functions before the template is rendered
	created() {},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		activateTab(index) {
			this.activeTab = index
		},

		getCheckboxItems(data) {
			if (data.action === 'add') {
				this.checkedItems = [...this.checkedItems, data.node]
			} else if (data.action === 'delete') {
				this.checkedItems = this.checkedItems.filter(function(name) { return name !== data.node })
			}
		},

		showSelectionOntologyDialog(data) {
			this.$emit('get-selected-ontology', data)
			/* this.$emit('background') */
		},
	},
}

</script>
<style scoped>

.search-tree-overlay-container {
	display:flex;
	z-index: 100;
	max-width: 100%;
	max-height: 100%;
	position: sticky;
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
	font-weight: bold;
}

.ausschlusskriterien-overlay {
	right: 0;
}

.ontology-search-tree-header {
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

.ontology-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
}

.ontology-tab.active {
	background-color: #738cba;
	color: white;
}

.ontology-container {
	height: 650px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.ontologyNode {
	height: 570px;
	padding: 20px 25px 20px 25px;
}

.ontology-button {
	display: flex;
	column-gap: 15px;
	margin: 20px;
	justify-content: flex-end;
	flex-direction: row;
}

.ontology-button button {
	border-radius: 8px;
}

.ontology-loading {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 150px;
	border-top: 2px solid #adbcd7;
}

</style>
