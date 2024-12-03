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
				<div v-for="(option, index) in selectedCriterion.filterOptions"
					:key="index"
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
import type { ConceptOptionsData, ConceptType } from '../../types/ConceptOptionsData'
import type { Criterion } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'ConceptOptions',
	props: {
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
			selectedValue: (this.selectedCriterion.selectedFilter as ConceptType)?.valueFilter?.selectedConcepts
				?? [],
		}
	},
	watch: {
		selectedValue() {
			this.conceptType = this.selectedValue.length > 0
				? {
					valueFilter: {
						selectedConcepts: this.selectedValue,
						type: 'concept',
					},
				}
				: undefined

			this.$emit('get-selected-filter-option', this.conceptType)

			if (this.conceptType && this.conceptType.valueFilter) {
				this.$emit('toggle-reset-button', true)
			} else {
				this.$emit('toggle-reset-button', false)
			}
		},

		isResetDisabled(): void {
			if (this.isResetDisabled) {
				this.selectedValue = []
			}
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
