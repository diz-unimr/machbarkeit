<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
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
				<div v-if="ontology.selectable === true" class="search-tree-term-entry">
					<input :id="ontology.termCodes[0].code"
						ref="ontologyCheckbox"
						v-model="isCheckboxSelected"
						:value="ontology.display"
						type="checkbox"
						@change="$emit('add-checkbox-selected', ontology.termCodes[0].code, isCheckboxSelected)">
					<p @click="() => (state = !state)">
						{{ ontology.display }}
					</p>
				</div>
				<div v-else
					class="search-tree-term-entry">
					<p @click="() => (state = !state)">
						{{ ontology.display }}
					</p>
				</div>
			</div>
			<ul v-show="state">
				<template v-for="(node, index) in ontology.children">
					<OntologyNestedTreeNode v-if="node.hasOwnProperty('children')"
						:key="index"
						:ontology="node"
						@update-ontology="updateOntology"
						@add-checkbox-selected="addCheckboxSelected" />
					<OntologyTreeNode v-if="!node.hasOwnProperty('children')"
						:key="index"
						:ontology="node"
						@update-ontology="updateOntology"
						@add-checkbox-selected="addCheckboxSelected" />
				</template>
			</ul>
		</li>
	</div>
</template>

<script>
import OntologyTreeNode from './OntologyTreeNode.vue'
import { ref } from 'vue'

export default {
	name: 'OntologyNestedTreeNode',
	components: {
		OntologyTreeNode,
	},
	props: {
		ontology: {
			type: Object,
			default: Object,
		},
		isHeadNode: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			state: ref(false),
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			selectedOntology: [],
			selectedOntologyList: [],
			updatedObject: {},
			isCheckboxSelected: false,
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (this.isHeadNode === true) {
			this.state = true
		}
	},
	beforeMount() {
	},
	mounted() {
		this.updatedObject = this.ontology
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		isExpanded(key) {
			return this.expandedGroup.indexOf(key) !== -1
		},

		/* addCheckboxSelected(ontology) {
			console.log('ontology')
			console.log(ontology)
			this.ontology2 = { ...ontology }
			this.ontology2.checkboxSelected = this.isCheckboxSelected
			console.log('ontology2')
			console.log(this.ontology2)
		}, */

		addCheckboxSelected(id, isCheckboxSelected) {
			this.$emit('add-checkbox-selected', id, isCheckboxSelected)
		},

		updateOntology(ontology) {
			/* this.$emit('update-ontology', ontology) */
		},

		/* getSelectedOntology1() {
			this.selectedOntologyList = this.ontologyList
			const arrIndex = this.activeTab.toString() + this.ontologyIndex.toString()
			if (this.selectedOntology.length > 0) {
				const ontologyId = this.$refs.ontologyCheckbox.id
				this.selectedOntologyList.push([arrIndex, this.selectedOntology[0], ontologyId])
			} else {
				const index = this.selectedOntologyList.findIndex(subarray => subarray.includes(arrIndex))
				this.selectedOntologyList.splice(index, 1)
			}
		}, */
	},
}
</script>

<style scoped>
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
