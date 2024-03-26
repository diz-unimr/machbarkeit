<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div id="ontology-tree" class="ontology-tree">
		<li style="list-style-type: none;">
			<div class="ontology-head-node">
				<button>
					<img src="http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png">
				</button>
				<div v-if="ontology.selectable === true" class="search-tree-term-entry">
					<input type="checkbox">
					<label>
						{{ ontology.display }}
					</label>
				</div>
			</div>
		</li>
	</div>
</template>

<script>
export default {
	name: 'OntologyTreeNode',
	props: {
		ontology: {
			type: Object,
			default: Object,
		},
	},
	data() {
		return {}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
	},
	beforeMount() {
	},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {},
}
</script>

<style scoped>

#ontology-tree {
	overflow-y: auto;
	height: 100%;
}

.ontology-head-node {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: center flex-start;
	align-items: center;
}

.search-tree-term-entry {
	display: flex;
	flex-direction: row;
	align-items: center;
}

.search-tree-term-entry input {
	margin: 0px 10px;
}

.search-tree-term-entry label {
	padding-left: 10px;
}

input[type='checkbox'] {
	width: 15px;
	height: 15px;
}

img {
	height: 12px;
	width: 12px;
	display: none;
}

button {
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	width: 20px;
}

</style>
