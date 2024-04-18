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
							<div id="ontology-nested-tree" class="ontology-nested-tree-node">
								<li style="list-style-type: none;">
									<div class="ontology-head-node">
										<button @click="() => (state = !state)">
											<img class="expandImg"
												:src="state
													? imgExpand
													: imgCollapse
												">
										</button>
										<div class="search-tree-term-entry">
											<p @click="() => (state = !state)">
												{{ ontology.display }}
											</p>
										</div>
									</div>

									<ul v-show="state">
										<div v-for="(child, index) in ontology.children" :key="index">
											<!-- :is-head-node="true" -->
											<OntologyNestedTreeNode v-if="child.children"
												:ontology="child"
												@input="getCheckboxItems" />
											<OntologyTreeNode v-if="!child.children"
												:ontology="child"
												@input="getCheckboxItems" />
										</div>
									</ul>
								</li>
							</div>
						</div>
					</div>
					<div class="ontology-button">
						<button @click="$emit('select-ontology', selectedOntologyArr)">
							AUSWÄHLEN
						</button>
						<button @click="$emit('update-status', criteriaId)">
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
import OntologyTreeNode from './OntologyTreeNode.vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { ref } from 'vue'

export default {
	name: 'SearchTreeOverlayContent',
	components: {
		OntologyNestedTreeNode,
		OntologyTreeNode,
	},
	props: {
		isAusschlusskriterien: {
			type: Boolean,
			default: false,
		},
		filteredArr: {
			type: Array,
			default: Array,
		},
		criteria: {
			type: String,
			default: '',
		},
		criteriaId: {
			type: String,
			default: '',
		},
	},
	data() {
		return {
			activeTab: 0,
			ontologyModulName: [],
			ontologyArray: [],
			selectedOntologyArr: [],
			responseArray: [],
			checkedItems: [],
			state: ref(true),
			isCheckboxSelected: [],
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	async beforeCreate() {
		const jsonData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology'))
		this.responseArray = jsonData.data
		console.log(this.responseArray)
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

		updateOntology(ontology) {
			this.$emit('update-array', ontology)
		},

		getCheckboxItems(data) {
			if (data.action === 'add') {
				this.checkedItems = [...this.checkedItems, data.node]
			} else if (data.action === 'delete') {
				this.checkedItems = this.checkedItems.filter(function(name) { return name !== data.node })
			}
			console.log(this.checkedItems)
		},
	},
}

</script>
<style scoped>

.ontology-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
}

.ontology-tab.active {
	background-color: #738cba;
	color: white;
}

.tab-content > div.show {
	display: block;
}

li {
	list-style-type: none;
}

ul {
	padding-inline-start: 40px;
}

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

.ontology-tree {
	max-height: 50vh;
	overflow-y: auto;
	height: 35vh;
	margin: 1em;
}

.ontology-list {
	display: flex;
	flex-direction: row;
	column-gap: 8px;
	min-height: 30px;
	padding-left: 15px;
	align-items: center;
}

.ontology-loading {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 150px;
	border-top: 2px solid #adbcd7;
}

.search-tree-term-entry {
	display: flex;
	flex-direction: row;
	align-items: center;
}

.search-tree-term-entry input {
	margin: 0px 10px;
}

.modul-name {
	display: flex;
	flex-direction: row;
	align-items: center;
}

img {
	margin: 0px 10px;
	width: 12px;
	height: 12px
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

button {
	border-radius: 8px;
}

input[type='checkbox'] {
	width: 15px;
	height: 15px;
}

#ontology-nested-tree {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
}

.ontology-head-node {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: center flex-start;
	align-items: center;
}

.ontology-head-node button {
	display: flex;
	flex-direction: row;
	align-items: center;
	width: auto;
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
}

.search-tree-term-entry {
	display: flex;
	flex-direction: row;
	align-items: center;
	margin-left: 5px;
}

.search-tree-term-entry input {
	margin: 0px 10px;
}

.search-tree-term-entry p {
	font-size: 16px;
	font-weight: 400;
	padding-left: 10px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

img {
	height: 12px;
	width: 12px;
}

ul {
	margin-left: 40px;
}

img imgCollapse {
	transform: rotate(90deg);
}

</style>
