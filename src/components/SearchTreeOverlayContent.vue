<!-- eslint-disable vue/valid-v-show -->
<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="search-tree-overlay-container">
		<div class="search-tree-overlay-wrapper" :class="{ 'ausschlusskriterien-overlay': isAusschlusskriterien }">
			<div class="criteria-name">
				{{ criteria }}
			</div>
			<div v-if="responseArray.length > 0">
				<div class="ontology-search-tree-header">
					<div class="search-tree-button">
						<!-- <div>Alle</div> -->
						<div v-for="(groupName, groupName_index) in ontologyGroupName"
							:key="groupName_index"
							class="ontology-tab"
							:class="{ 'active': activeTab === groupName_index }"
							@click="activateTab(groupName_index)">
							{{ groupName }}
						</div>
					</div>
				</div>
				<div class="ontology-container">
					<div v-for="(ontology, ontology_index) in responseArray"
						v-show="activeTab === ontology_index"
						:key="ontology_index">
						<div class="ontologyNode">
							<OntologyNestedTreeNode v-if="ontology.hasOwnProperty('children')"
								:ontology="ontology"
								:is-head-node="isHeadNode" />
							<OntologyTreeNode v-if="!ontology.hasOwnProperty('children')" :ontology="ontology" />
						</div>
					</div>
					<div class="ontology-button">
						<button>AUSWÄHLEN</button>
						<button>ABBRECHEN</button>
					</div>
				</div>
			</div>
			<div v-else class="ontology-loading">
				Loading...
			</div>
		</div>
	</div>
</template>

<!-- <NcActionCheckbox>
		{{ responseArray[5]['children'][0]['display'] }}
	</NcActionCheckbox>
	<NcActionCheckbox>
		{{ responseArray[5]['children'][1]['display'] }}
</NcActionCheckbox> -->

<script>
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'
import OntologyTreeNode from './OntologyTreeNode.vue'

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
		ontologyGroupName: {
			type: Array,
			default: Array,
		},
		responseArray: {
			type: Array,
			default: Array,
		},
		filteredArr: {
			type: Array,
			default: Array,
		},
		criteria: {
			type: String,
			default: '',
		},
	},
	data() {
		return {
			activeTab: 0,
			isHeadNode: true,
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		activateTab(index) {
			this.activeTab = index
		},
		/* ontologyName() {
			this.ontologyGroupName = this.filteredArr.map(
				(item) =>
					item[
						'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
					],
			)
			// filter duplicate
			this.ontologyGroupName = this.ontologyGroupName.filter(
				(item, index) =>
					this.ontologyGroupName.indexOf(item) === index && item !== undefined && item !== '',
			)
		}, */
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

/* .tab-content > div {
  display: none;
} */

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

</style>
