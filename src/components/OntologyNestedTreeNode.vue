<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div id="ontology-nested-tree" class="ontology-nested-tree-node">
		{{ ontologyName }}
		<!-- {{ getChildren(ontology) }} -->

		<!-- <div v-for="(node_children, index_children) in node['children']" :key="index_children">
			<div v-if="node_children['leaf'] === true" class="ontology-tree">
				<div class="ontology-tree-node">
					<li>
						<div v-if="node_children['selectable'] === true" class="search-tree-term-entry">
							<input type="checkbox">
							{{ node_children['display'] }}
						</div>
					</li>
				</div>
			</div>
			<div v-if="node_children['leaf'] === false" class="ontology-tree">
				<div class="ontology-tree-node">
					<li>
						<a class="modul-name">
							<img src="http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png">
							<div v-if="node_children['selectable'] === true" class="search-tree-term-entry">
								<input type="checkbox">
								{{ node_children['display'] }}
							</div>
							<div v-if="node_children['selectable'] === false" class="search-tree-term-entry">
								{{ node_children['display'] }}
							</div>
						</a>
					</li>
				</div>
			</div>
		</div> -->
	</div>
</template>

<script>

export default {
	name: 'OntologyNestedTreeNode',
	props: {
		ontology: {
			type: Array,
			default: Array,
		},
		ontologyName: {
			type: String,
			default: String,
		},
		index: {
			type: Number,
			default: Number,
		},
	},
	data() {
		return {
			id: 0,
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		// this.getChildren(this.ontology)
	},
	beforeMount() {},
	mounted() {
		this.getChildren(this.ontology)
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		getChildren(ontology) {
			console.log(ontology)

			/*

			console.log(this.id)

			// Get the parent div by its ID
			const parent = document.getElementById('ontology-nested-tree')

			// Create a new div element
			const newDiv = document.createElement('div')
			newDiv.id = 'tree-level_' + this.id
			// Append the new div as a child of the parent div
			parent.appendChild(newDiv) */

			for (const node in ontology) {
				this.id++
				// console.log(ontology[node].display)
				// Create a HEAD div element
				const newDiv = document.createElement('div')
				newDiv.id = 'tree-level_' + this.id
				newDiv.textContent = ontology[node].display
				// console.log(newDiv)
				document.getElementById('ontology-nested-tree').appendChild(newDiv)

				// Child
				if (ontology[node].children !== undefined) {
					// console.log('children - not leaf')
					/* const newDiv = document.createElement('div')
						newDiv.innerText = ontology[node].display
						document.body.appendChild(newDiv)

						newDiv.id = 'tree-level_' + this.id
						newDiv.innerHTML = "<input type='checkbox'> Hi </input>" */
					this.getChildren(ontology[node].children)
				} else { // No Child
					// console.log('leaf')
				}
				this.id--
			}

		},
	},
}
</script>
