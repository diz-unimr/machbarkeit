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
						:checked="isChecked"
						:disabled="onlySwlCode"
						type="checkbox"
						:value="criterion?.display"
						@change="toggleCheckedbox">
					<div class="search-tree-term-entry">
						<div class="swl-wrapper" :style="{gap: swlCode ? '1.5%' : '0'}">
							<p class="swl-code" @click="state = !state">
								{{ swlCode }}
							</p>
							<p class="swl-description" :style="{cursor: criterion.leaf ? 'default' : 'pointer'}" @click="state = !state">
								{{ criterion?.display }}
							</p>
						</div>
						<p v-if="onlySwlCode && criterion.selectable === true" class="swl-code-warning">
							(Die Suche nach SWL-Code wird momentan nicht unterstützt)
						</p>
					</div>

					<!-- <div v-else-if="!criterion?.selectable" class="search-tree-term-entry">
						<p class="swl-code">
							HK
						</p>
						<p class="swl-desciption" @click="() => (state = !state)">
							{{ criterion?.display }}
						</p>
					</div> -->
				</div>
				<ul v-show="state">
					<template v-if="criterion.children && criterion.children.length > 0">
						<OntologyNestedTreeNode v-for="child in criterion.children"
							:key="child.id"
							:criterion="child"
							:parent="criterion.selectable ? criterion : null"
							:level="level + 1"
							@change="checkboxTrigger" />
					</template>
				</ul>
			</li>
		</keep-alive>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'
import { mapGetters, mapMutations, mapActions } from 'vuex'

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
}

export default Vue.extend({
	name: 'OntologyNestedTreeNode2',
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
		level: {
			type: Number,
			required: true,
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
		/* isChecked: {
			get(): boolean {
				console.log(this.$store.getters.getCheckedItems(this.criterion.id))
				return this.$store.getters.getCheckedItems(this.criterion.id)
			},
		}, */

		...mapGetters(['getCheckedItems']),
		isChecked(): boolean {
			// console.log('computed', this.criterion)
			const isItemCheked = this.getCheckedItems(this.criterion.id)
			return isItemCheked
		},
		/* isChecked: {
			get() {
				return (this as any).getCheckedItems(this.criterion.id)
			},
			set(value) {
				this.toggleCheckedItem({
				id: this.criterion.id,
				isChecked: (event.target as HTMLInputElement).checked,
				ownChildren: this.criterion.children,
				ownParent: this.parent,
			})
			}
		} */
	},

	watch: {
		// watch for changes in the state if element is checked
		isChecked: {
			// immediate: true,
			handler(newValue) {
				console.log('watch', newValue, this.criterion)
				this.state = newValue
				// this.toggleChildren()
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		console.log('created', this.isChecked)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		...mapMutations({
			toggleCheckedItem: 'TOGGLE_CHECKED_ITEM',
			toggleParent: 'TOGGLE_PARENT',
			toggleChild: 'TOGGLE_CHILD',
		}),
		// ...mapActions({ toggleCheckedItem: 'TOGGLE_CHECKED_ITEM' }),

		checkboxTrigger(checkedItem: CheckedItem): void {
			console.log('checkboxTrigger', checkedItem)
			this.$emit('change', checkedItem)
		},

		toggleCheckedbox(event: Event): void {
			console.log('toggleCheckedbox', this.criterion)
			const isChecked = (event.target as HTMLInputElement).checked
			console.log(isChecked)
			// this.emitChange(isChecked, this.criterion, this.loinc)

			this.toggleCheckedItem(this.criterion.id, isChecked)
			// if criterion is parent and has children
			if (this.criterion.children && this.criterion.children.length > 0) {
				this.toggleChildren(this.criterion.children, isChecked)
			}
			if (this.parent) {
				this.toggleParent()
			}

		},
		toggleChildren(children: Criterion[], isChecked: boolean): void {
			children.forEach((child: Criterion) => {
				this.toggleCheckedItem(this.criterion.id, isChecked)
				this.toggleChild(child.id)
			})
		},

		toggleParent(): void {
			console.log('toggleParent', this.criterion)
		},

		handleChildChange(checkedItem: CheckedItem): void {
			this.emitChange(checkedItem)

			// if criterion is child and has parent
			if (this.parent) {
				console.log('parent', this.parent)
				this.toggleParent()
			}
			// if criterion is parent and has children
			if (this.criterion.children && this.criterion.children.length > 0) {
				console.log('children', this.criterion.children)
				this.criterion.children.forEach((child: Criterion) => {
					// action = (event.target as HTMLInputElement).checked ? 'uncheck' : 'check'
					// this.emitChange(action, child, this.loinc)
				})
				this.toggleChildren()

			}

			/* this.$emit('change', {
				action: (event.target as HTMLInputElement).checked ? 'check' : 'uncheck',
				node: this.criterion,
				loinc: this.loinc,
			}) */
			this.toggleCheckedItem(this.criterion.id)
		},

		emitChange(action: string, child: Criterion, loinc: string): void {
			console.log('emitChange')
			this.$emit('change', {
				action,
				node: child,
				loinc,
			})
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

.ontology-head-node input[type="checkbox"]:disabled {
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
