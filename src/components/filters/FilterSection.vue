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
				<p>{{ filterName }}</p>
			</div>
			<div class="filter-card__button--reset">
				<button :disabled="isResetDisabled" @click="reset">
					ZURÜCKSETZEN
				</button>
			</div>
		</div>

		<TimeRangeOptions v-if="selectedCriterion && selectedCriterion.timeRestrictionAllowed"
			:index="index"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			:time-restriction-data="timeRestriction"
			@handle-filter-change="handleFilterChange" />
		<QuantityOptions v-if="selectedCriterion.filterType && selectedCriterion.filterType === 'quantity'"
			:index="index"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@handle-filter-change="handleFilterChange" />
		<ConceptOptions v-if="selectedCriterion.filterType && selectedCriterion.filterType === 'concept'"
			:index="index"
			:is-reset-disabled="isResetDisabled"
			:selected-criterion="selectedCriterion"
			@handle-filter-change="handleFilterChange" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import TimeRangeOptions from './controls/TimeRangeOptions.vue'
import ConceptOptions from './controls/ConceptOptions.vue'
import QuantityOptions from './controls/QuantityOptions.vue'
import type { Criterion } from '../../types/OntologyPanelData'
import type { ConceptType } from '../../types/ConceptOptionsData'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRangeType, TimeRangeFilter } from '../../types/TimeRangeOptionsData'

interface FilterSectionData {
	filterName: string | null;
	state: boolean;
	isResetDisabled: boolean;
	imgExpand: string;
}

// FilterPayload
export interface FilterInfo {
	index: number;
	id: string;
	value: NonNullable<ConceptType['valueFilter'] | QuantityType['valueFilter'] | TimeRangeType>;
	type: string;
	isFilterComplete?: boolean;
	hasFilterValue?: boolean;
}

export default Vue.extend({
	name: 'FilterSection',
	components: {
		TimeRangeOptions,
		ConceptOptions,
		QuantityOptions,
	},
	props: {
		index: {
			type: Number,
			required: true,
		},
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			required: true,
		},
		isStateEditFilter: {
			type: Boolean,
			default: false,
		},
		timeRestriction: {
			type: Object as PropType<TimeRangeFilter>,
			default: undefined,
		},
	},
	data(): FilterSectionData {
		const hasConceptFilter = Array.isArray(this.selectedCriterion.valueFilter) && this.selectedCriterion.valueFilter?.length > 0
		const hasQuantityFilter = typeof this.selectedCriterion.valueFilter === 'object' && Object.keys(this.selectedCriterion.valueFilter).length > 0
		const hasTimeRestriction = typeof this.selectedCriterion.timeRestriction === 'object' && Object.keys(this.selectedCriterion.timeRestriction).length > 0
		return {
			filterName: this.selectedCriterion
				? this.selectedCriterion.timeRestrictionAllowed
					? 'Zeitraum (Option)'
					: 'Wertbereich'
				: null,
			state: this.isStateEditFilter,
			isResetDisabled: !(this.selectedCriterion && (hasConceptFilter || hasQuantityFilter || hasTimeRestriction)),
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
		}
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

	methods: {
		handleFilterChange(payload: FilterInfo): void {
			if (payload.hasFilterValue) {
				this.isResetDisabled = false
			} else {
				this.isResetDisabled = true
			}
			this.$emit('update-filter', payload)
		},

		reset(): void {
			this.isResetDisabled = true
		},
	},
})
</script>

<style scoped>
.filter-card-wrapper {
	max-height: 54px;
	transition: max-height 1s linear;
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 8px 20px;
	margin-bottom: 20px;
	overflow: hidden;
	position: relative;
}

.filter-card__expand {
	max-height: 350px;
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
