<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="filter-dialog-container">
		<div class="filter-dialog-wrapper">
			<div class="filter-dialog">
				<div class="filter-dialog__title">
					Einschränkungen der ausgewählten Merkmale
				</div>
				<div v-if="selectedCriteria.length > 1 && selectedCriteria[0]?.timeRestrictionAllowed && !isStateEditFilter" class="dropdown-wrapper">
					<TimeRangeOptions label="master filter" @set-all-filter="replaceAllDateChanged" />
				</div>
				<div class="filter-dialog__panel">
					<FilterCard v-for="(selectedCriterion, index) in selectedCriteria"
						:key="selectedCriterion.id"
						:index="index"
						:filter-option="selectedCriterion.filterOptions ? selectedCriterion.filterOptions : null"
						:selected-criterion="selectedCriterion"
						:is-state-edit-filter="isStateEditFilter"
						:time-restriction="timeRestriction"
						@update-filter="updateFilter"
						@delete-dialog-card="deleteDialogCard" />
				</div>
				<div class="filter-dialog__button-group">
					<button :disabled="!selectedCriteria.every(criterion => criterion.filterCompleteStatus === true)" @click="submit">
						<template v-if="isStateEditFilter">
							SPEICHERN
						</template>
						<template v-else>
							HINZUFÜGEN
						</template>
					</button>
					<button @click="cancel">
						ABBRECHEN
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import FilterCard from './FilterCard.vue'
import type { Criterion } from '../../types/OntologyPanelData'
import type { ConceptType } from '../../types/ConceptOptionsData'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRangeFilter, TimeRangeType } from '../../types/TimeRangeOptionsData'
import type { FeasibilityQueryBuilderData } from '../../types/FeasibilityQueryBuilderData'
import type { FilterInfo } from './FilterSection.vue'
import TimeRangeOptions from './controls/TimeRangeOptions.vue'

interface FilterPanelData {
	selectedCriteria: Criterion[];
	timeRestriction?: TimeRangeFilter;
}

export default Vue.extend({
	name: 'FilterPanel',
	components: {
		FilterCard,
		TimeRangeOptions,
	},
	props: {
		criteriaType: {
			type: String,
			required: true,
		},
		isStateEditFilter: {
			type: Boolean,
			default: false,
		},
		editFilterInformation: {
			type: Object as PropType<FeasibilityQueryBuilderData['editFilterInformation']>,
			default: undefined,
		},
	},

	data(): FilterPanelData {
		return {
			selectedCriteria: [],
			timeRestriction: undefined,
		}
	},

	computed: {
		sourceCriteria() {
			let criteria = this.isStateEditFilter && this.editFilterInformation
				? [this.$store.getters.getEditCharacteristic(this.editFilterInformation.id, this.editFilterInformation.index, this.editFilterInformation.criteriaType)]
				: Object.keys(this.$store.state.selectedItems).length > 0
					? Object.values(this.$store.state.selectedItems)
					: []
			criteria = criteria.map((criterion: Criterion) => {
				if (!criterion.filterCompleteStatus) {
					criterion.filterCompleteStatus = true
				} return criterion
			})
			return criteria
		},
	},

	watch: {
		sourceCriteria: {
			handler(newValue) {
				// deep copy object and array
				this.selectedCriteria = JSON.parse(JSON.stringify(newValue))
			},
			deep: true,
			immediate: true,
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

	methods: {
		deleteDialogCard(index: number, id: string): void {
			if (this.selectedCriteria !== null && !this.editFilterInformation) {
				if (this.selectedCriteria[index].id === id) {
					this.selectedCriteria.splice(index, 1)
				}
			}
			this.selectedCriteria.length === 0 && this.$emit('dialog-close') && this.$store.dispatch('clearSelectedItems')
		},

		updateFilter(payload: FilterInfo): void {
			this.selectedCriteria = this.selectedCriteria.map((criterion: Criterion, index: number) => {
				criterion.filterCompleteStatus = payload.isFilterComplete
				if (payload.id === criterion.id && payload.index === index && payload.isFilterComplete && payload.value) {
					if (payload.type === 'concept') {
						criterion.valueFilter = Object.keys(payload.value).length > 0 ? { ...payload.value } as ConceptType['valueFilter'] : undefined
					} else if (payload.type === 'quantity') {
						criterion.valueFilter = Object.keys(payload.value).length > 0 ? { ...payload.value } as QuantityType['valueFilter'] : undefined
					} else if (payload.type === null) {
						criterion.timeRestriction = Object.keys(payload.value).length > 0 ? { ...payload.value } as TimeRangeType['timeRestriction'] : undefined
					}
				}
				return criterion
			})
		},

		submit(): void {
			this.$emit('dialog-close')
			const length = this.selectedCriteria.length
			this.isStateEditFilter && this.$store.dispatch('updateFilterOptions', { section: this.editFilterInformation.criteriaType, index: this.editFilterInformation.index, newFilterOptions: this.selectedCriteria })
			!this.isStateEditFilter && this.$store.dispatch('addSelectedCharacteristics', { section: this.criteriaType, characteristics: this.selectedCriteria, logic: Array.from({ length }, () => 'and') })
			this.$store.dispatch('clearCheckedItems')
			this.$store.dispatch('clearSelectedItems')
		},

		cancel(): void {
			this.$emit('dialog-close')
			this.$store.dispatch('clearCheckedItems')
			this.$store.dispatch('clearSelectedItems')
		},

		replaceAllDateChanged(filterValue: TimeRangeFilter): void {
			this.timeRestriction = { ...filterValue }
		},
	},
})
</script>

<style scoped>
/* @media (max-width: 1300px) {
	.filter-dialog-container {
		top: 0;
	}
	.filter-dialog {
		max-height: 85vh;
	}
} */

.filter-dialog-container {
	display: flex;
	position: relative;
	z-index: 100;
	width: 100%;
	margin: 0px auto 0px auto;
	max-height: 100%;
}

.filter-dialog-wrapper {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
	max-height: 850px;
}

.filter-dialog {
	display: flex;
	flex-direction: column;
	width: 100%;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.filter-dialog__title {
	z-index: 1000;
	opacity: 1;
	padding: 5px;
	margin: 0 0 20px!important;
	border-bottom-width: 2px;
	border-bottom-style: solid;
	border-bottom-color: #5a78ae;
	font-size: 24px;
	font-weight: 600;
	font-family: Roboto,Helvetica Neue,sans-serif;
}

.dropdown-wrapper {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.dropdown-wrapper button {
	border-radius: 8px;
	margin: 0px 20px;
	height: fit-content;
}

.filter-dialog__panel {
	overflow-y: auto;
	overflow-x: hidden;
	margin-bottom: 20px;
}

.filter-dialog__button-group {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

.filter-dialog__button-group button {
	border-radius: 8px;
}
</style>
