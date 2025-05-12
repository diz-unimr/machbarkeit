<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<li>
			<div class="ontology-head-node">
				<button v-if="!criterion?.leaf" @click="expandTreeNode">
					<img :src="isExpanded
						? imgExpand
						: imgCollapse">
				</button>
				<input v-if="criterion?.selectable"
					:id="String(criterion?.id)"
					v-model="isChecked"
					:disabled="onlySwlCode"
					type="checkbox"
					:value="criterion?.display">
				<div class="search-tree-term-entry">
					<div class="terminology-code-wrapper" :style="{gap: terminologyCode ? '1.5%' : '0'}">
						<p class="terminology-code" @click="expandTreeNode">
							{{ criterion?.selectable && terminologyCode ? terminologyCode : null }}
						</p>
						<p class="terminology-description" :style="{cursor: criterion.leaf ? 'default' : 'pointer'}" @click="expandTreeNode">
							{{ criterion?.display }}
						</p>
					</div>
					<p v-if="onlySwlCode && criterion.selectable === true" class="only-swl-code-warning">
						(Die Suche nach SWL Code wird momentan nicht unterstützt)
					</p>
				</div>
			</div>
			<ul v-if="isExpanded">
				<template v-if="criterion.children">
					<OntologyTreeNode v-for="child in criterion.children"
						:key="child.id"
						:module-name="moduleName"
						:criterion="child"
						:parent="criterion"
						:parents="[...parents, criterion]"
						@change="toggleParent" />
				</template>
			</ul>
		</li>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'

interface OntologyTreeNodeData {
	isExpanded: boolean;
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
	onlySwlCode: boolean;
	terminologyCode: string | undefined;
	loinc: string | undefined;
}

export interface CheckedItem {
	action: string;
	node: Criterion;
	loinc?: string;
	parentIds?: string[];
}

export default Vue.extend({
	name: 'OntologyTreeNode',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<Criterion>,
			default: null,
		},
		moduleName: {
			type: String,
			default: '',
		},
		parent: {
			type: Object as PropType<Criterion>,
			default: null,
		},
		parents: {
			type: Array as PropType<Criterion[]>,
			default: () => [],
		},
		parentIds: {
			type: Array as PropType<string[]>,
			default: () => [],
		},
	},

	data(): OntologyTreeNodeData {
		return {
			isExpanded: false,
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			concepts: null,
			terminologyCode: undefined,
			onlySwlCode: false,
			loinc: undefined,
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get(): boolean {
				return this.$store.getters.getCheckedItems(this.criterion)
			},
			// Updates checked items when checkbox state changes
			set(checked: boolean): void {
				if (checked) {
					this.$store.dispatch('addCheckedItem', { id: this.criterion.id, node: this.criterion })
					// if parent is checked, check all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.toggleChildren(this.criterion.children, checked)
					}
					// if all children are checked, check the parent
					if (this.parent && this.parent.selectable) {
						this.toggleParent(checked)
					}
				} else {
					this.$store.dispatch('removeCheckedItem', this.criterion)
					// if parent is unchecked, uncheck all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.toggleChildren(this.criterion.children, checked)
					}
					// if child is unchecked, uncheck the parent
					if (this.parent && this.parent.selectable) {
						this.toggleParent(checked)
					}
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		[this.onlySwlCode, this.terminologyCode] = this.findTerminologyCode(this.criterion)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		toggleChildren(children: Criterion[], isChecked: boolean): void {
			children.forEach((child: Criterion) => {
				isChecked ? this.$store.dispatch('addCheckedItem', { id: child.id, node: child }) : this.$store.dispatch('removeCheckedItem', child)
				if (child.children && child.children.length > 0) {
					this.toggleChildren(child.children, isChecked)
				}
			})
		},

		toggleParent(isChecked: boolean): void {
			if (!isChecked) {
				this.$store.dispatch('removeCheckedItem', this.parent)
				if (this.parent.parentId !== null && this.parent.selectable) {
					this.$emit('change', isChecked) // go back to upper parent level
				}
			} else {
				const checkedItemsMap = this.$store.state.checkedItemsMap
				const allChildrenChecked = this.parent.children.every(child => checkedItemsMap.has(child.id))
				if (allChildrenChecked) {
					this.$store.dispatch('addCheckedItem', { id: this.parent.id, node: this.parent })
					if (this.parent.parentId !== null && this.parent.selectable) {
						this.$emit('change', isChecked) // go back to upper parent level
					}
				}
			}
		},

		expandTreeNode(): void {
			this.isExpanded = !this.isExpanded
		},

		findTerminologyCode(criterion: Criterion): [boolean, string | undefined] {
			if (this.moduleName === 'Laboruntersuchung') {
				const onlySwlCode = criterion.termCodes?.every((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
				const terminologyCode = criterion.termCodes?.find((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')?.code
				return [onlySwlCode, terminologyCode]
			} else {
				const terminologyCode = criterion.termCodes[0]?.code
				return [false, terminologyCode]
			}
		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding: 0 10px 0 15px;
}

.ontology-nested-tree-node li {
	list-style-type: none;
}

/* .ontology-nested-tree-node li:not(:first-child) {
	margin-left: 15px;
} */

.ontology-head-node {
	display: flex;
	/* place-content: center flex-start;
	align-items: flex-start; */
	margin-top: 5px;
	gap: clamp(10px, 1.5%, 15px);
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
	flex-direction: column;
	width: 100%;
}

.ontology-head-node input[type='checkbox'] {
	width: 15px;
	height: 15px;
	margin: 0px;
}

.ontology-head-node input[type='checkbox']:disabled {
	cursor: default;
}

.search-tree-term-entry .terminology-code {
	margin-top: 7px;
	font-weight: 500;
	font-size: 14px;
}

.search-tree-term-entry .terminology-description {
	max-width: fit-content;
	margin-top: 5px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.no-checkbox {
	display: inline-block;
	width: 17px;
}

.terminology-code-wrapper {
	display: flex;
	gap: 1.5%;
}

.only-swl-code-warning {
	font-size: 12px;
	color: #DC3545;
	cursor: default;
}

img {
	height: 15px;
	width: 15px;
}

ul {
	margin-left: 45px;
}
</style>
