<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container dialog-border">
		<div class="content-option__text">
			Geben Sie einen oder mehrere zulässige Werte an:
		</div>
		<div class="content-option__body">
			<div class="content-option__checkbox">
				<div v-for="(option, index_option) in selectedCriterion.filterOptions"
					:key="index_option"
					class="checkbox-option">
					<!-- https://kyoshee.medium.com/building-custom-checkbox-using-only-html-and-css-no-js-1-babd79d5e2e9 -->
					<input v-model="selectedValue"
						type="checkbox"
						:value="option">
					<label>{{ option.display }}</label>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { ConceptOptionsData, ConceptType } from '../../../types/ConceptOptionsData'
import type { Criterion } from '../../../types/OntologyPanelData'

export default Vue.extend({
	name: 'ConceptOptions',
	props: {
		index: {
			type: Number,
			required: true,
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			required: true,
		},
	},
	data(): ConceptOptionsData {
		return {
			selectedValue: Array.isArray((this.selectedCriterion.valueFilter as ConceptType['valueFilter'])?.selectedConcepts)
				? (this.selectedCriterion.valueFilter as ConceptType['valueFilter'])?.selectedConcepts
				: [],
		}
	},

	watch: {
		isResetDisabled(newValue): void {
			if (newValue && this.selectedValue.length > 0) {
				this.selectedValue = []
			}
		},
		selectedValue(newValue) {
			this.$emit('handle-filter-change', {
				index: this.index,
				id: this.selectedCriterion.id,
				value: newValue.length > 0
					? {
						selectedConcepts: newValue,
						type: this.selectedCriterion.filterType,
					}
					: {},
				type: this.selectedCriterion.filterType,
				isFilterComplete: true,
				hasFilterValue: newValue.length > 0,
			})
		},
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

	methods: {},
})
</script>

<style scoped>
.content-option-container {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
}

.dialog-border {
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 10px 20px;
	margin-bottom: 20px;
}

.content-option__text {
	font-weight: 500;
}

.content-option__text span {
	color: red;
}

.content-option__body {
	overflow-y: auto;
	max-height: 170px;
}

.content-option__checkbox {
	margin: 20px 20px 5px 20px;
}

.checkbox-option {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
}

.content-option-alert {
	font-weight: 500;
	color: red;
}

input[type='checkbox'] {
	width: 15px;
	height: 15px;
	margin: 0px;
}
</style>
