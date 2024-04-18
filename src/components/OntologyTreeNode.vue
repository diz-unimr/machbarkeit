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
				</button>
				<div v-if="ontology.selectable === true" class="search-tree-term-entry">
					<input :id="ontology.id"
						v-model="isChecked"
						type="checkbox"
						:value="ontology.display">
					<p>
						{{ ontology.display }}
					</p>
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
		updateOntology: {
			type: Function,
			default: () => {},
		},
		selectedElementArray: {
			type: Boolean,
			default: Boolean,
		},
	},

	data() {
		return {
			selectedOntology: [],
			selectedOntologyList: [],
			selectedElement: [],
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
	created() {},
	beforeMount() {
	},
	mounted() {},
	beforeUpdate() {},
	updated() {
	},
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
