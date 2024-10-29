<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<keep-alive>
			<li>
				<div class="ontology-head-node">
					<button @click="expandTreeNode()">
						<img v-if="!criterion?.leaf"
							:src="state
								? imgExpand
								: imgCollapse
							">
						<div v-else
							class="img-display-none" />
					</button>
					<div v-if="criterion?.selectable" class="search-tree-term-entry">
						<input :id="String(criterion?.id)"
							v-model="isChecked"
							type="checkbox"
							:value="criterion?.display">
						<p @click="() => (state = !state)">
							{{ criterion?.display }}
						</p>
					</div>
					<div v-else-if="!criterion?.selectable"
						class="search-tree-term-entry">
						<p @click="() => (state = !state)">
							{{ criterion?.display }}
						</p>
					</div>
				</div>
				<ul v-show="state">
					<template v-if="criterion.children">
						<OntologyNestedTreeNode v-for="child in criterion.children"
							:key="child.id"
							:criterion="child"
							@input="checkboxTrigger" />
					</template>
				</ul>
			</li>
		</keep-alive>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeData {
	state: boolean;
	imgCollapse: string;
	imgExpand: string;
	concepts: [
		{
			display: string;
			id: number;
			leaf: boolean;
			moduleId: number;
			parentId: number | null;
			selectable: boolean;
		}
	] | null;
}

export interface CheckedItem {
	action: string;
	node: OntologyTreeElement;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNode',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<OntologyTreeElement>,
			default: null,
		},
	},

	data(): OntologyNestedTreeNodeData {
		return {
			state: false,
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			concepts: null,
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get(): boolean {
				return this.$store.getters.checkedItems(this.criterion.id)
			},
			// Updates checked items when checkbox state changes
			set(checked: boolean): void {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion })
					this.$store.dispatch('addCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
					this.$store.dispatch('removeCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				}
			},
		},
		/* isChecked: {
			// Determines if the current item is checked
			get(): string {
				return ''
			},
			// Updates checked items when checkbox state changes
			set(checked): void {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
				}
			},
		}, */
	},

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
		checkboxTrigger(checkedItem: CheckedItem): void {
			this.$emit('input', checkedItem)
		},

		/* async getConcepts(num: number): Promise<void> {
			try {
				if (this.state === true) {
					const responseConcepts = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology/' + num))
				}
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
			}
		}, */

		expandTreeNode(): void {
			this.state = !this.state
			// this.getConcepts(moduleId)

		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding-right: 10px;
	margin-left: 20px;
}

.ontology-nested-tree-node li {
	list-style-type: none;
}

.ontology-head-node {
	display: flex;
	place-content: center flex-start;
	align-items: flex-start;
	margin-top: 5px;
	gap: 20px;
}

.ontology-head-node button {
	display: flex;
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
	align-items: flex-start;
	gap: 20px;
}

.search-tree-term-entry input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin: 0px;
}

.search-tree-term-entry p {
	flex: 1;
	margin-top: 5px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.img-display-none {
	cursor: default;
	height: 15px;
	width: 15px;
}

img {
	height: 15px;
	width: 15px;
}

ul {
	margin-left: 30px;
}
</style>
