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
					<input :id="ontology.id"
						v-model="isChecked"
						type="checkbox"
						:value="ontology.display">
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
				<template v-for="child in ontology.children">
					<OntologyNestedTreeNode v-if="child.children"
						:key="child.id"
						v-model="checkedItems"
						:ontology="child"
						@input="checkboxTrigger" />
					<OntologyTreeNode v-if="!child.children"
						:key="child.id"
						v-model="checkedItems"
						:ontology="child"
						@input="checkboxTrigger" />
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
		modelValue: {
			type: String,
			default: String,
		},
		selectedValues: {
			type: Array,
			default: () => [],
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
			selectedElement: [],
			selectedElementArray: [],
			checkedItems: [],
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get() {
				  return 0/* this.value.includes(this.ontology.display) */
			},
			// Updates checked items when checkbox state changes
			set(checked) {
				if (checked) {
					this.$emit('input', { action: 'add', node: this.ontology })
				} else {
					this.$emit('input', { action: 'delete', node: this.ontology })
				}
			},
		},
	},

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

		checkboxTrigger(data) {
			this.$emit('input', data)
		},
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
