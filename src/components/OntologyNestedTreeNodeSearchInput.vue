<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div>
		<div v-if="filteredCriterion !== null && filteredCriterion.selectable === true" class="ontology-nested-tree-node-wrapper">
			<p class="criterion-code">
				{{ filteredCriterion.termCodes?.[0].code }}
			</p>
			<div class="search-tree-term-entry">
				<input :id="criterion.id"
					v-model="isChecked"
					type="checkbox"
					:value="criterion.display">
				<p>
					{{ filteredCriterion.display }}
				</p>
			</div>
		</div>

		<OntologyNestedTreeNodeSearchInput v-for="child in criterion.children"
			:key="child.id"
			:criterion="child"
			:index="index"
			:inclusion-search-input="inclusionSearchInput"
			:exclusion-search-input="exclusionSearchInput"
			@check-existing-data="checkExistingData"
			@input="checkboxTrigger" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeSearchInputData {
	filteredCriterion: OntologyTreeElement | null;
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
		index: {
			type: Number,
			default: Number,
		},
		inclusionSearchInput: {
			type: String,
			default: '',
		},
		exclusionSearchInput: {
			type: String,
			default: '',
		},
	},

	data(): OntologyNestedTreeNodeSearchInputData {
		return {
			filteredCriterion: null,
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
		inclusionSearchInput() {
			if (this.inclusionSearchInput.length > 0 && this.criterion.selectable === true) {
				const filteredCriterionResult = this.filterCriteria(this.inclusionSearchInput, this.criterion)
				filteredCriterionResult && this.$emit('check-existing-data')
			}
		},

		exclusionSearchInput() {
			if (this.exclusionSearchInput.length > 0 && this.criterion.selectable === true) {
				const filteredCriterionResult = this.filterCriteria(this.exclusionSearchInput, this.criterion)
				filteredCriterionResult && this.$emit('check-existing-data')
			}
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (this.inclusionSearchInput.length > 0 && this.criterion.selectable === true) {
			const filteredCriterionResult = this.filterCriteria(this.inclusionSearchInput, this.criterion)
			filteredCriterionResult && this.$emit('check-existing-data')
		}

		if (this.exclusionSearchInput.length > 0 && this.criterion.selectable === true) {
			const filteredCriterionResult = this.filterCriteria(this.exclusionSearchInput, this.criterion)
			filteredCriterionResult && this.$emit('check-existing-data')
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
				this.filteredCriterion = criterion
			} else this.filteredCriterion = null
			return this.filteredCriterion
		},

		checkExistingData() {
			this.$emit('check-existing-data', this.index)
		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node-wrapper {
	display: flex;
	margin-top: 5px;
}

.search-tree-term-entry {
	display: flex;
	align-items: flex-start;
	margin-left: 5px;
	width: 100%;
	height: 100%;
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

.criterion-code {
	font-weight: 500;
	width: 15%;
	margin-top: 5px;
}
</style>
