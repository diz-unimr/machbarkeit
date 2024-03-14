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
			<div class="ontology-group-name">
				<div class="search-tree-button">
					<div>Alle</div>
					<div>Consent</div>
					<div>Diagnose</div>
					<div>Fall</div>
					<div>Labor</div>
					<div>Lufu</div>
					<div>Medikation</div>
					<div>Person</div>
					<div>Prozedur</div>
					<!-- <div v-for="groupName in ontologyGroupName" :key="groupName">
						{{ groupName }}
					</div> -->
				</div>
			</div>
			<!-- <OntologyTree :filltered-arr="filteredArr" /> -->
			<div class="ontology-tree">
				{{ ontologyName() }}
				{{ ontologyGroupName }}
			</div>
		</div>
	</div>
</template>

<script>
// import OntologyTree from './OntologyTree.vue'
export default {
	name: 'SearchTreeOverlayContent',
	components: {
		// OntologyTree,
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
	},
	data() {
		return {
			ontologyGroupName: [],
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
		/* test() {
			console.log('Hello')
		}, */
		ontologyName() {
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
	box-sizing: border-box;
	min-height: 12em;
	background-color: #fff;
	box-shadow: 0 11px 15px -4px #0003, 0 24px 38px 5px #00000024, 0 9px 46px 8px #0000001f;
	pointer-events: auto;
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

.ontology-group-name {
	display: flex;
	flex-direction: row;
	box-sizing: border-box;
	place-content: stretch flex-start;
	align-items: stretch;
	border-top: 2px solid #adbcd7;
	border-bottom: 2px solid #adbcd7;
	overflow-x: auto;
}

.search-tree-button {
	display: flex;
	margin: auto;
	padding: 0px 10px;
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
	font-family: Roboto, monospace !important;
}
</style>
