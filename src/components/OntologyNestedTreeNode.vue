<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<keep-alive>
			<li>
				<div class="ontology-head-node">
					<button v-if="!criterion?.leaf" @click="state = !state">
						<img :src="state
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
						<div class="swl-wrapper" :style="{gap: swlCode ? '1.5%' : '0'}">
							<p class="swl-code" @click="expandTreeNode">
								{{ swlCode }}
							</p>
							<p class="swl-description" :style="{cursor: criterion.leaf ? 'default' : 'pointer'}" @click="expandTreeNode">
								{{ criterion?.display }}
							</p>
						</div>
						<p v-if="onlySwlCode && criterion.selectable === true" class="swl-code-warning">
							(Die Suche nach SWL-Code wird momentan nicht unterstützt)
						</p>
					</div>
				</div>
				<ul v-show="state">
					<template v-if="criterion.children">
						<OntologyNestedTreeNode v-for="child in criterion.children"
							:key="child.id"
							:criterion="child"
							:parent="criterion"
							:parent-ids="[...parentIds, criterion.id]"
							@input="checkboxTrigger"
							@change="toggleParent(parent, isChecked)" />
					</template>
				</ul>
			</li>
		</keep-alive>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'

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
	onlySwlCode: boolean;
	swlCode: string | undefined;
	loinc: string | undefined;
}

export interface CheckedItem {
	action: string;
	node: Criterion;
	loinc?: string;
	parentIds?: string[];
}

export default Vue.extend({
	name: 'OntologyNestedTreeNode',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<Criterion>,
			default: null,
		},
		parent: {
			type: Object as PropType<Criterion>,
			default: null,
		},
		parentIds: {
			type: Array as PropType<string[]>,
			default: null,
		},
	},

	data(): OntologyNestedTreeNodeData {
		return {
			state: false,
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			concepts: null,
			swlCode: undefined,
			onlySwlCode: false,
			loinc: undefined,
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get(): boolean {
				return this.$store.getters.getCheckedItems(this.criterion.id)
			},
			// Updates checked items when checkbox state changes
			set(checked: boolean): void {
				if (checked) {
					this.$store.dispatch('addCheckedItem', { node: this.criterion, isChecked: checked })
					// if parent is checked, check all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.toggleChildren(this.criterion.children, checked)
					}
					// if all children are checked, check the parent
					if (this.parent) {
						this.toggleParent(this.parent, checked)
					}
					this.$emit('input', { action: 'check', node: this.criterion })
				} else {
					this.$store.dispatch('removeCheckedItem', { id: this.criterion.id, isChecked: checked })
					// if parent is unchecked, uncheck all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.criterion.children.forEach((child) => {
							this.$emit('input', { action: 'uncheck', node: child, parentIds: this.parentIds })
							this.$store.dispatch('removeCheckedItem', { id: child.id, isChecked: checked })
						})
					}
					// if child is unchecked, uncheck the parent
					if (this.parent) {
						this.$emit('input', { action: 'uncheck', node: this.parent, parentIds: this.parentIds })
						this.parentIds.forEach((parentId) => {
							this.$store.dispatch('removeCheckedItem', { id: parentId, isChecked: checked })
						})
					}
					this.$emit('input', { action: 'uncheck', node: this.criterion, parentIds: this.parentIds })
				}
			},
		},
	},

	watch: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.findSWL(this.criterion)
	},
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

		toggleChildren(children: Criterion[], isChecked: boolean): void {
			children.forEach((child: Criterion) => {
				this.$store.dispatch('addCheckedItem', { node: child, isChecked })
				if (child.children && child.children.length > 0) {
					this.toggleChildren(child.children, isChecked)
				}
			})
		},

		toggleParent(parent: Criterion, isChecked: boolean): void {
			if (this.parent && this.parent.selectable) {
				const children = new Set(parent.children)
				const checkedItemId = new Set(this.$store.state.checkedItems.map((checkedItem: Criterion) => checkedItem.id))
				const allChildrenChecked = [...children].every(child => checkedItemId.has(child.id))

				if (allChildrenChecked) {
					this.$store.dispatch('addCheckedItem', { node: this.parent, isChecked })
					this.$emit('change') // go back to upper parent level
					this.$emit('input', { action: 'check', node: parent })
				}
			}
		},

		expandTreeNode(): void {
			this.state = !this.state
		},

		findSWL(criterion: Criterion): void {
			const onlySwlCode = criterion.termCodes?.every((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
			// const loinc = criterion.termCodes?.find((termCode) => termCode.system === 'http://loinc.org' || termCode.system === 'http://snomed.info/sct')
			const swlcode = criterion.termCodes?.find((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
			this.onlySwlCode = onlySwlCode
			this.swlCode = swlcode?.code ?? undefined
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
	margin-left: 15px;
}

.ontology-nested-tree-node li {
	list-style-type: none;
}

/* .ontology-nested-tree-node li:not(:first-child) {
	margin-left: 15px;
} */

.ontology-head-node {
	display: flex;
	place-content: center flex-start;
	align-items: flex-start;
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
	/* align-items: flex-start;
	gap: clamp(10px, 1.5%, 15px);
	 */
}

.ontology-head-node input[type='checkbox'] {
	width: 15px;
	height: 15px;
	margin: 0px;
}

.ontology-head-node input[type='checkbox']:disabled {
	cursor: default;
}

.search-tree-term-entry .swl-code {
	margin-top: 5px;
	font-weight: 500;
	font-size: 14px;
}

.search-tree-term-entry .swl-description {
	max-width: fit-content;
	margin-top: 5px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.no-checkbox {
	display: inline-block;
	width: 17px; /* ขนาดที่เท่ากับ checkbox */
}

.swl-wrapper {
	display: flex;
	gap: 1.5%;
	/* gap: clamp(10px, 1.5%, 15px); */
}

.swl-code-warning {
	font-size: 12px;
	color: #DC3545;
	/* margin-top: 5px; */
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
