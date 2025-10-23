<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<li>
			<div class="ontology-head-node">
				<button v-if="!criterion?.leaf && criterion.children" @click="expandTreeNode">
					<img :src="isExpanded
						? imgExpand
						: imgCollapse">
				</button>
				<input v-if="criterion?.selectable"
					:id="String(criterion?.id)"
					v-model="isChecked"
					type="checkbox"
					:value="criterion?.display">
				<div class="search-tree-term-entry">
					<div class="terminology-wrapper">
						<div v-if="moduleName === 'Laboruntersuchung' && criterion.selectable" class="terminology-code-wrapper" @click="expandTreeNode">
							<span class="terminology-code">{{ criterion?.selectable && loinc ? loinc : 'Kein Loinc' }}
								<div class="hover-modal">
									{{ loinc ? 'Loinc: ' + loinc : 'Kein Loinc' }}
								</div>
							</span><span class="separator">|</span>
						</div>
						<div v-if="moduleName === 'Laboruntersuchung' && criterion.selectable" class="terminology-code-wrapper" @click="expandTreeNode">
							<span class="terminology-code">{{ criterion?.selectable && swlCode ? swlCode : 'Kein Swisslab Code' }}
								<div class="hover-modal">
									{{ swlCode ? 'Swisslab Code: ' + swlCode : 'Kein Swisslab Code' }}
								</div>
							</span><span class="separator">|</span>
						</div>
						<div v-if="moduleName !== 'Laboruntersuchung' && criterion?.selectable && termCode" class="terminology-code-wrapper" @click="expandTreeNode">
							<span class="terminology-code">{{ termCode }}
								<div class="hover-modal">
									{{ termCode ? 'Terminologie Code: ' + termCode : 'Kein Terminologie Code' }}
								</div>
							</span><span class="separator">|</span>
						</div>
						<div v-if="criterion?.selectable || criterion?.children?.length > 0"
							class="terminology-description-wrapper"
							:style="{cursor: criterion.leaf ? 'default' : 'pointer'}"
							@click="expandTreeNode">
							<span :style="{fontSize: criterion.selectable ? '15px' : '16px'}">
								{{ criterion?.display }}
								<div v-if="criterion.selectable" class="hover-modal">
									Beschreibung
								</div>
							</span>
						</div>
					</div>
					<!-- <p v-if="moduleName === 'Laboruntersuchung' && !isLoincSupport && criterion.selectable === true" class="only-swl-code-warning">
						(Die Suche nach SWL Code wird momentan nicht unterstützt)
					</p> -->
				</div>
			</div>
			<ul v-if="isExpanded">
				<template v-if="criterion.children">
					<OntologyTreeNode v-for="child in criterion.children"
						:key="child.id"
						:module-name="moduleName"
						:criterion="child"
						:current-theme="currentTheme"
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
import type { Criterion, Module } from '../types/OntologyPanelData'

interface OntologyTreeNodeData {
	isExpanded: boolean;
	imgCollapse: string;
	imgExpand: string;
	termCode?: string | null;
	swlCode?: string | null;
	loinc?: string | null ;
	// isLoincSupport: boolean;
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
		currentTheme: {
			type: String,
			default: 'default',
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
			imgCollapse: process.env.BASE_URL + '/img/arrow-collapse-blue.png',
			imgExpand: process.env.BASE_URL + '/img/arrow-expand.png',
			termCode: undefined,
			swlCode: undefined,
			loinc: undefined,
			// isLoincSupport: false,
		}
	},

	computed: {
		modules() {
			return this.$store.state.modules
		},

		isChecked: {
			// Determines if the current item is checked
			get(): boolean {
				return this.$store.getters.getCheckedItem(this.criterion.id)
			},
			// Updates checked items when checkbox state changes
			set(checked: boolean): void {
				// let selectedItem = this.setCriterionContext(this.criterion) // Get the selected item
				let selectedItem = this.criterion
				if (checked) {
					this.$store.dispatch('addCheckedItem', this.criterion.id)
					// if parent is checked, check all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.toggleChildren(this.criterion.children, checked)
					}
					// if all children are checked, check the parent
					if (this.parent && this.parent.selectable) {
						selectedItem = this.toggleParent(checked, selectedItem)
					}
					this.$store.dispatch('addSelectedItem', { key: selectedItem.id, item: selectedItem })
				} else {
					this.$store.dispatch('removeCheckedItem', this.criterion.id)
					// if parent is unchecked, uncheck all children
					if (this.criterion.children && this.criterion.children.length > 0) {
						this.toggleChildren(this.criterion.children, checked)
					}
					// if child is unchecked, uncheck the parent
					if (this.parent && this.parent.selectable) {
						selectedItem = this.toggleParent(checked, selectedItem)
					}
					this.$store.dispatch('removeSelectedItem', selectedItem.id)
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		[this.termCode, this.loinc, this.swlCode] = this.findTerminologyCode(this.criterion)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		/* setCriterionContext(criterion: Criterion) {
			if (this.modules) {
				const module = this.modules.find((module: Module) => module.id === criterion.moduleId)
				criterion.context = {
					code: module.fdpgCdsCode || '',
					display: module.name || '',
					system: module.fdpgCdsSystem || '',
					version: module.version || '',
				}
				criterion.color = module.color
				return criterion
			}
		}, */

		toggleChildren(children: Criterion[], isChecked: boolean): void {
			children.forEach((child: Criterion) => {
				if (isChecked) {
					this.$store.dispatch('addCheckedItem', child.id)
					this.$store.dispatch('removeSelectedItem', child.id)
				} else {
					this.$store.dispatch('removeCheckedItem', child.id)
					this.$store.dispatch('removeSelectedItem', child.id)
				}
				if (child.children && child.children.length > 0) {
					this.toggleChildren(child.children, isChecked)
				}
			})
		},

		toggleParent(isChecked: boolean, selectedItem: Criterion): Criterion {
			/* if (!this.parent) {
				const parent = this.$store.getters.getOntologyInfo(this.criterion.moduleId, this.criterion.parentId, this.criterion.id)
			} */
			if (this.parent) {
				if (isChecked) {
					const checkedItems = this.$store.state.checkedItems
					const areAllChildrenChecked = this.parent.children.every(child => checkedItems.includes(child.id))
					if (areAllChildrenChecked) {
						this.$store.dispatch('addCheckedItem', this.parent.id)
						selectedItem = this.parent
						if (this.parent.parentId !== null && this.parent.selectable) {
							this.parent.children.forEach((child: Criterion) => {
								this.$store.dispatch('removeSelectedItem', child.id)
							})
							this.$emit('change', isChecked, selectedItem) // go back to upper parent level
						}
					}
				} else {
					if (this.parent.parentId !== null && this.parent.selectable) {
						if (this.$store.getters.getCheckedItem(this.parent.id)) {
							this.$store.dispatch('removeCheckedItem', this.parent.id)
						}
						if (this.$store.getters.getSelectedItem(this.parent.id)) {
							this.$store.dispatch('removeSelectedItem', this.parent.id)
						}

						// check for siblings and add them to selected items
						this.parent.children.forEach((child: Criterion) => {
							const isChildChecked = this.$store.getters.getCheckedItem(child.id)
							isChildChecked && this.$store.dispatch('addSelectedItem', { key: child.id, item: child })
						})
						this.$emit('change', isChecked, selectedItem) // go back to upper parent level
					}
				}
			}
			return selectedItem
		},

		expandTreeNode(): void {
			this.isExpanded = !this.isExpanded
		},

		findTerminologyCode(criterion: Criterion): [string | null | undefined, string | null | undefined, string | null | undefined] {
			const termCode = undefined
			const loinc = undefined
			const swlCode = undefined
			// const isLoincSupport = false
			if (this.moduleName === 'Laboruntersuchung') {
				const loinc = criterion.termCodes?.find((termCode) => termCode.system === 'http://loinc.org')?.code
				const swlCode = criterion.termCodes?.find((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')?.code
				// const isLoincSupport = loinc !== undefined && loinc !== null
				return [termCode, loinc, swlCode]
			} else {
				const termCode = criterion.termCodes[0]?.code
				return [termCode, loinc, swlCode]
			}
		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node {
	/* overflow-y: auto; */
	overflow: visible;
	scrollbar-width: auto;
	/* height: 100%; */
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
	align-items: flex-start;
	width: auto;
	text-decoration: none;
	background-color: unset;
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

.search-tree-term-entry .terminology-code-wrapper {
	margin-top: 7px;
	font-size: 14px;
	min-width: fit-content;
}

.terminology-wrapper {
	display: flex;
}

.terminology-code-wrapper span {
	white-space: nowrap;
}

.terminology-code {
	display: inline-block;
	position: relative;
	font-weight: 500;
}

.hover-modal {
	display: none;
	position: absolute;
	top: -14px;
	min-width: 50px;
	padding: 0px 2px;
	background: #fff;
	border: 1px solid #bbb;
	border-radius: 3px;
	box-shadow: 0 4px 16px rgba(0,0,0,0.12);
	z-index: 100;
	font-size: 9px;
	font-weight: normal;
}

.separator {
	margin: 0 7px;
}

.terminology-description-wrapper span {
	display: inline-block;
	position: relative;
}

.search-tree-term-entry .terminology-description-wrapper {
	max-width: fit-content;
	margin-top: 5px;
}

/* .terminology-description-wrapper .hover-modal {
	top: -12px;
} */

.terminology-code:hover .hover-modal,
.terminology-description-wrapper span:hover .hover-modal {
	display: block;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.no-checkbox {
	display: inline-block;
	width: 17px;
}

.only-swl-code-warning {
	font-size: 12px;
	color: #DC3545;
	cursor: default;
}

img {
	height: 15px;
	width: 15px;
	margin-top: 10px;
}

ul {
	margin-left: 45px;
}
</style>
