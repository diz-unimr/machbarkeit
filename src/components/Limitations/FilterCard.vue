<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div :class="['filter-card-wrapper', {'filter-card__expand': state}]">
		<div class="filter-card__header">
			<div class="filter-card__button--expand-filter">
				<button @click="state = !state">
					<img :src="imgExpand"
						:style="{transform: state ? 'rotate(180deg)': 'rotate(0deg)'}">
				</button>
				<p>{{ display }} </p>
				<p v-if="display === 'Zeitraum' || profile.valueDefinition?.optional">
					(optional)
				</p>
			</div>
			<div class="filter-card__button--reset">
				<button :disabled="isResetDisabled" @click="reset">
					ZURÜCKSETZEN
				</button>
			</div>
		</div>
		<component :is="filterType"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@toggle-reset-button="toggleResetButton"
			@get-selected-filter-option="getSelectedFilterOption" />
		<!-- <TimeRangeOptions v-if="profile.timeRestrictionAllowed && display === 'Zeitraum'"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@toggle-reset-button="toggleResetButton"
			@get-selected-filter-option="getSelectedFilterOption" />

		<QuantityOptions v-if="profile.valueDefinition?.type === 'quantity' && display === 'Wertebereich'"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@toggle-reset-button="toggleResetButton"
			@get-selected-filter-option="getSelectedFilterOption" />

		<ConceptOptions v-if="profile.valueDefinition?.type === 'concept' && display === 'Wertebereich'"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@toggle-reset-button="toggleResetButton"
			@get-selected-filter-option="getSelectedFilterOption" /> -->
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import TimeRangeOptions from './TimeRangeOptions.vue'
import ConceptOptions from './ConceptOptions.vue'
import QuantityOptions from './QuantityOptions.vue'
import type { Profile } from '../../types/FeasibilityQueryBuilderData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData'
import type { ConceptType } from '../../types/ConceptOptionsData'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRange } from '../../types/TimeRangeOptionsData'

interface FilterCardData {
	state: boolean;
	isResetDisabled: boolean;
	filterType: string | null;
	imgExpand: string;
}

export default Vue.extend({
	name: 'FilterCard',
	components: {
		TimeRangeOptions,
		ConceptOptions,
		QuantityOptions,
	},
	props: {
		profile: {
			type: Object as PropType<Profile>,
			required: true,
		},
		attribute: {
			type: String,
			default: String,
		},
		isFilterOptional: {
			type: Boolean,
			default: true,
		},
		selectedCriterion: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
		display: {
			type: String,
			default: String,
		},
	},
	data(): FilterCardData {
		return {
			state: this.profile.valueDefinition ? !this.profile.valueDefinition?.optional : false, // set state = false for optional Filter
			isResetDisabled: this.display === 'Wertebereich' ? !(this.selectedCriterion.conceptType?.value || this.selectedCriterion.quantityType?.value) : (!this.selectedCriterion.timeRange?.value.type || this.selectedCriterion.timeRange?.value.type === 'kein Filter'),
			filterType: null,
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
		}
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (this.profile.timeRestrictionAllowed && this.display === 'Zeitraum') { this.filterType = 'time-range-options' }
		if (this.profile.valueDefinition?.type === 'quantity' && this.display === 'Wertebereich') { this.filterType = 'quantity-options' }
		if (this.profile.valueDefinition?.type === 'concept' && this.display === 'Wertebereich') { this.filterType = 'concept-options' }
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		setState(): void {
			if (!this.profile.valueDefinition?.optional) {
				this.state = true
			}
		},

		getSelectedFilterOption(status: string, selectedFilterInfo: ConceptType | QuantityType | TimeRange) {
			this.$emit('get-selected-filters', status, selectedFilterInfo)
		},

		toggleResetButton(isReset: boolean): void {
			this.isResetDisabled = !isReset
		},

		reset(): void {
			this.isResetDisabled = true
		},
	},
})
</script>

<style scoped>
.filter-card-wrapper {
	max-height: 52px;
	transition: max-height 1s linear;
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 5px 20px;
	margin-bottom: 20px;
	overflow: hidden;
	position: relative;
}

.filter-card__expand {
	max-height: 315px;
}

.filter-card__header {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
}

.filter-card__button--expand-filter {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
	justify-content: flex-start;
}

.filter-card__button--expand-filter button {
	display: flex;
	flex-direction: row;
	align-items: center;
	width: auto;
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
}

.filter-card__button--expand-filter img {
	transition: all 0.5s ease-in
}

.filter-card__button--reset button {
	font-size: 14px;
}

button {
	border-radius: 8px;
}

img {
	height: 16px;
	width: 16px;
}
</style>
