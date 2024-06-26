<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option dialog-card">
		<div class="content-option__header">
			<span>*</span> Geben Sie einen oder mehrere zulässige Werte an:
		</div>
		<div class="content-option__body">
			<div class="content-option-checkbox">
				<div v-for="(code, index) in profile.valueDefinition?.selectableConcepts"
					:key="index"
					class="checkbox-option">
					<!-- https://kyoshee.medium.com/building-custom-checkbox-using-only-html-and-css-no-js-1-babd79d5e2e9 -->
					<input v-model="selectedConcepts"
						type="checkbox"
						:value="code.display">
					<label>{{ code.display }}</label>
				</div>
			</div>
		</div>
		<div v-if="selectedConcepts?.length <= 0" class="content-option-alert">
			Wählen Sie mindestens einen Wert
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { ConceptTypeData } from '../../types/ConceoptTypeData'
import type { Profile } from '../../types/FeasibilityQueryBuilderData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'ConceptType',
	props: {
		profile: {
			type: Object as PropType<Profile>,
			required: true,
		},
		toggleResetButton: {
			type: Function,
			default: () => {},
		},
		getSelectedOption: {
			type: Function,
			default: () => {},
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		selectedOntology: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
	},
	data(): ConceptTypeData {
		return {
			selectedConcepts: this.selectedOntology.conceptType ? this.selectedOntology.conceptType.value : [],
			isFilterOptional: this.profile.valueDefinition?.optional,
		}
	},
	watch: {
		selectedConcepts() {
			this.$emit('get-selected-option', {
				type: 'conceptType',
				value: this.selectedConcepts,
				isFilterOptional: this.isFilterOptional,
				completeFilter: this.selectedConcepts.length > 0,
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
		this.$emit('get-selected-option', {
			type: 'conceptType',
			value: this.selectedConcepts,
			isFilterOptional: this.isFilterOptional,
			completeFilter: false,
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
input[type='checkbox'] {
	width: 15px;
	height: 15px;
	margin: 0px;
}

.content-option {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
}

.content-option__header {
	font-weight: 500;
}

.content-option__header span {
	color: red;
}

.content-option__body {
	overflow-y: auto;
	max-height: 170px;
}

.content-option-checkbox {
	margin: 20px 20px 5px 20px;
}

.content-option-alert {
	font-weight: 500;
	color: red;
}

.checkbox-option {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
}

.dialog-card {
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 10px 20px;
	margin-bottom: 20px;
}
</style>
