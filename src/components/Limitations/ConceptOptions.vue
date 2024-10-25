<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container dialog-border">
		<div class="content-option__text">
			<span>*</span> Geben Sie einen oder mehrere zulässige Werte an:
		</div>
		<div class="content-option__body">
			<div class="content-option__checkbox">
				<div v-for="(option, index) in filterOption.filterOptions"
					:key="index"
					class="checkbox-option">
					<!-- https://kyoshee.medium.com/building-custom-checkbox-using-only-html-and-css-no-js-1-babd79d5e2e9 -->
					<input v-model="selectedConcepts"
						type="checkbox"
						:value="option.display">
					<label>{{ option.display }}</label>
				</div>
			</div>
		</div>
		<div v-if="!filterOption.optional && selectedConcepts?.length <= 0" class="content-option-alert">
			Wählen Sie mindestens einen Wert
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { ConceptOptionsData } from '../../types/ConceptOptionsData'
import type { FilterOptions } from '../../types/LimitationsSelectedCriteriaModalData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'ConceptOptions',
	props: {
		filterOption: {
			type: Object as PropType<FilterOptions>,
			required: true,
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		selectedCriterion: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
	},
	data(): ConceptOptionsData {
		return {
			selectedConcepts: this.selectedCriterion.conceptType?.value
				? this.selectedCriterion.conceptType.value
				: [],
		}
	},
	watch: {
		selectedConcepts() {
			this.$emit('get-selected-filter-option', {
				type: 'conceptType',
				display: this.selectedCriterion.display,
				isFilterOptional: this.filterOption.optional,
				isFilterComplete: this.filterOption.optional ? true : this.selectedConcepts.length > 0,
				value: this.selectedConcepts,
			})

			if (this.selectedConcepts.length > 0) {
				this.$emit('toggle-reset-button', true)
			} else {
				this.$emit('toggle-reset-button', false)
			}
		},

		isResetDisabled(): void {
			if (this.isResetDisabled) {
				this.selectedConcepts = []
			}
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.$emit('get-selected-filter-option', {
			type: 'conceptType',
			display: this.selectedCriterion.display,
			isFilterOptional: this.filterOption.optional,
			isFilterComplete: this.selectedCriterion.conceptType?.isFilterComplete
				? this.selectedCriterion.conceptType?.isFilterComplete
				: this.filterOption.optional,
			value: this.selectedConcepts,
		})
	},

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
