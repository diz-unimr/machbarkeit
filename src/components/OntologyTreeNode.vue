<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div id="ontology-tree" class="ontology-tree">
		<li style="list-style-type: none;">
			<div class="ontology-head-node">
				<button>
					<img src="">
					<div v-if="ontology.selectable === true" class="search-tree-term-entry">
						<input :id="ontology.id"
							:value="ontology.display"
							type="checkbox"
							@change="checkboxClicked">
						<p>
							{{ ontology.display }}
						</p>
					</div>
					<!-- v-if="ontology.selectable === false" -->
					<div v-else
						class="search-tree-term-entry">
						<p>
							{{ ontology.display }}
						</p>
					</div>
				</button>
				<!-- <button>
					<img src="http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png">
				</button>
				<div v-if="ontology.selectable === true" class="search-tree-term-entry">
					<input type="checkbox">
					<label>
						{{ ontology.display }}
					</label>
				</div> -->
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

	methods: {
		checkboxClicked() {
			console.log('checkbox')
		},
	},
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

.ontology-head-node button {
	display: flex;
    flex-direction: row;
    align-items: center;
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	width: auto;
	margin: 1px 0px 1px 0px;
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

input[type='checkbox'] {
	width: 15px;
	height: 15px;
}

img {
	height: 12px;
	width: 12px;
	opacity: 0;
}

p {
	font-size: 16px;
	font-weight: 400;
}

</style>
