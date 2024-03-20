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
						<div>Alle</div>
						<div v-for="(groupName, index) in ontologyGroupName"
							:key="index"
							class="ontology-tab"
							:class="{ 'active': activeTab === index }"
							@click="activateTab(index)">
							{{ groupName }}
						</div>
					</div>
				</div>
				<OntologyNestedTreeNode :ontology="responseArray[0]['children']" :ontology-name="responseArray[0]['display']" /> <!-- :index="index" -->
				<!--<div v-for="(ontology, index) in responseArray" v-show="activeTab === index" :key="index">

					 <div v-if="node['leaf'] === true" class="ontology-tree">
						<div class="ontology-tree-node">
							<li>
								<a class="modul-name">
									<img src="http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png">
									{{ node['display'] }}
									leaf = true
								</a>
							</li>
						</div>
					</div>
					<div v-if="node['leaf'] === false" class="ontology-tree">
						<div class="ontology-nested-tree-node">
							<li>
								<a class="modul-name">
									<img src="http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png">
									<div v-if="node['selectable'] === true" class="search-tree-term-entry">
										<input type="checkbox">
										{{ node['display'] }}
									</div>
									<div v-if="node['selectable'] === false" class="search-tree-term-entry">
										{{ node['display'] }}
									</div>
								</a>

								<ul v-if="node['children'] !== undefined">
									<OntologyNestedTreeNode />
								</ul>
							</li>
						</div>
					</div>
				</div> -->
			</div>
			<div v-else class="ontology-loading">
				Loading
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
// import OntologyTree from './OntologyTree.vue'
// import { generateUrl } from '@nextcloud/router'
// import axios from '@nextcloud/axios'
import NcActionCheckbox from '@nextcloud/vue/dist/Components/NcActionCheckbox.js'
import OntologyNestedTreeNode from './OntologyNestedTreeNode.vue'

export default {
	name: 'SearchTreeOverlayContent',
	components: {
		// OntologyTree,
		NcActionCheckbox,
		OntologyNestedTreeNode,
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
			tabs: ['Tab 1', 'Tab 2', 'Tab 3'],
			tabContents: this.ontologyGroupName,
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
	box-shadow: 0 11px 15px -4px #0003, 0 24px 38px 5px #00000024, 0 9px 46px 8px #0000001f;
	pointer-events: auto;
	min-width: 94%;
	max-width: 100%;
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
	/* font-family: Roboto, monospace !important; */
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

</style>
