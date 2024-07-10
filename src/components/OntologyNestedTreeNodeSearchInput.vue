<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div>
		<div v-if="filteredCriteria !== null && filteredCriteria.selectable === true"
			class="search-tree-term-entry">
			<p class="filteredCriteriaCode">
				{{ filteredCriteria.termCodes?.[0].code }}
			</p>
			<div style="display: flex; align-items: flex-start; width: 85%;">
				<input :id="criterion.id"
					v-model="isChecked"
					type="checkbox"
					:value="criterion.display">
				<p style="margin-top: 4px;">
					{{ filteredCriteria.display }}
				</p>
			</div>
		</div>

		<OntologyNestedTreeNodeSearchInput v-for="child in criterion.children"
			:key="child.id"
			:criterion="child"
			:einschluss-text-serach="einschlussTextSerach"
			:ausschluss-text-serach="ausschlussTextSerach"
			@check-no-data="checkNoData"
			@input="checkboxTrigger" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeSearchInputData {
	filteredCriteria: OntologyTreeElement | null;
}

export interface CheckedItem {
	action: string;
	node: OntologyTreeElement;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNodeSearchInput',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
		einschlussTextSerach: {
			type: String,
			default: '',
		},
		ausschlussTextSerach: {
			type: String,
			default: '',
		},
		/* checkNoData: {
			type: Function,
			default: () => {},
		}, */
	},

	data(): OntologyNestedTreeNodeSearchInputData {
		return {
			filteredCriteria: null,
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get() {
				return ''
			},
			// Updates checked items when checkbox state changes
			set(checked) {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
				}
			},
		},
	},

	watch: {
		einschlussTextSerach() {
			if (this.einschlussTextSerach.length > 0 && this.criterion.selectable === true) {
				const filteredCriteria = this.filterCriteria(this.einschlussTextSerach, this.criterion)
				this.$emit('check-no-data', filteredCriteria)
			}
		},

		ausschlussTextSerach() {
			if (this.ausschlussTextSerach.length > 0 && this.criterion.selectable === true) {
				const filteredCriteria = this.filterCriteria(this.ausschlussTextSerach, this.criterion)
				filteredCriteria && this.$emit('check-no-data', filteredCriteria)
			}
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (this.einschlussTextSerach.length > 0 && this.criterion.selectable === true) {
			const filteredCriteria = this.filterCriteria(this.einschlussTextSerach, this.criterion)
			filteredCriteria && this.$emit('check-no-data', filteredCriteria)
		}

		if (this.ausschlussTextSerach.length > 0 && this.criterion.selectable === true) {
			const filteredCriteria = this.filterCriteria(this.ausschlussTextSerach, this.criterion)
			filteredCriteria && this.$emit('check-no-data', filteredCriteria)
		}
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

		filterCriteria(textSearch: string, criterion: OntologyTreeElement): OntologyTreeElement | null {
			const filteredItem = criterion.termCodes?.[0].code.toLowerCase().includes(textSearch.toLowerCase()) || criterion.termCodes?.[0].display.toLowerCase().includes(textSearch.toLowerCase())

			if (filteredItem) {
				this.filteredCriteria = criterion
				// eslint-disable-next-line vue/no-mutating-props
			} else this.filteredCriteria = null

			return this.filteredCriteria
		},

		checkNoData(filteredCriteria: OntologyTreeElement | null) {
			this.$emit('check-no-data', filteredCriteria)
		},
	},
})
</script>

<style scoped>
input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin-top: 0px;
}

.search-tree-term-entry {
	display: flex;
	flex-direction: row;
	align-items: flex-start;
	margin-left: 5px;
	width: 100%;
	height: 100%;
}

.search-tree-term-entry input {
	margin: 0px 10px;
}

.search-tree-term-entry p {
	font-size: 16px;
	font-weight: 400;
	padding-left: 10px;
}

.filteredCriteriaCode {
	font-weight: 500;
	width: 15%;
	margin-top: 4px;
}
</style>
