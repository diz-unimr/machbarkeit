<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="limitations-dialog-container">
		<div class="limitations-dialog-wrapper">
			<div class="limitations-dialog">
				<h2 class="limitations-dialog__title">
					Einschränkungen der ausgewählten Merkmale
				</h2>
				<div class="limitations-dialog__panel">
					<LimitationsSelectedCriteriaCard v-for="(selectedCriterion, index) in selectedCriteria"
						:id="index"
						:key="selectedCriterion.id"
						:filter-option="selectedCriterion.filterOptions ? selectedCriterion.filterOptions : null"
						:selected-criterion="selectedCriterion"
						:is-state-edit-filter="isStateEditFilter"
						@get-selected-criteria-filter="getSelectedCriteriaFilter(index, $event)"
						@delete-dialog-card="deleteDialogCard" />
				</div>
				<div class="limitations-dialog__button-group">
					<button :disabled="!isAllFilterComplete" @click="submit">
						<template v-if="isStateEditFilter">
							SPEICHERN
						</template>
						<template v-else>
							HINZUFÜGEN
						</template>
					</button>
					<button @click="$emit('dialog-close')">
						ABBRECHEN
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import LimitationsSelectedCriteriaCard from './LimitationsSelectedCriteriaCard.vue'
import type { Criterion } from '../../types/OntologySearchTreeModalData.ts'
import type { ConceptType } from '../../types/ConceptOptionsData'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRangeType } from '../../types/TimeRangeOptionsData'

interface LimitationsSelectedCriteriaModalData {
    selectedCriteriaFiltersInfo: Array<ConceptType | QuantityType | TimeRangeType>;
    isAllFilterComplete: boolean;
	filterCompleteStatus: Array<boolean>;
}

interface FilterInfo {
	selectedFiltersInfo: ConceptType | QuantityType | TimeRangeType | undefined;
	isFilterComplete: boolean
}

export default Vue.extend({
	name: 'LimitationsSelectedCriteriaModal',
	components: {
		LimitationsSelectedCriteriaCard,
	},
	props: {
		selectedCriteria: {
			type: Array<Criterion>,
			required: true,
		},
		isStateEditFilter: {
			type: Boolean,
			default: false,
		},
	},

	data(): LimitationsSelectedCriteriaModalData {
		return {
			selectedCriteriaFiltersInfo: [],
			isAllFilterComplete: true,
			filterCompleteStatus: [],
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
		deleteDialogCard(index: number): void {
			if (this.selectedCriteria !== null) {
				this.$emit('delete-selected-criteria', index)
				this.selectedCriteriaFiltersInfo.splice(index, 1)
				this.filterCompleteStatus.splice(index, 1)
				if (this.selectedCriteria.length === 0) {
					this.$emit('dialog-close')
				} else {
					this.isAllFilterComplete = !this.filterCompleteStatus.includes(false)
				}
			}
		},

		getSelectedCriteriaFilter(index: string | number, filterInfo: FilterInfo): void {
			if (filterInfo) {
				this.selectedCriteriaFiltersInfo[index] = filterInfo.selectedFiltersInfo
				this.filterCompleteStatus[index] = filterInfo.isFilterComplete
			}
			this.isAllFilterComplete = !this.filterCompleteStatus.includes(false)
		},

		submit(): void {
			this.$emit('get-selected-filter-info', this.selectedCriteriaFiltersInfo)
			this.$emit('dialog-close')
		},

	},
})
</script>

<style scoped>
@media (max-width: 1300px) {
	.limitations-dialog-container {
		top: -45%;
	}
	.limitations-dialog {
		max-height: 550px;
	}
}

.limitations-dialog-container {
	display: flex;
	position: relative;
	z-index: 100;
	width: 85%;
	margin: 0px auto 0px auto;
}

.limitations-dialog-wrapper {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
	max-height: 850px;
}

.limitations-dialog {
	display: flex;
	flex-direction: column;
	width: 100%;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.limitations-dialog__title {
	z-index: 1000;
	opacity: 1;
	padding: 5px;
	margin: 0 0 20px!important;
	border-bottom-width: 2px;
	border-bottom-style: solid;
	border-bottom-color: #5a78ae;
	font-weight: 600;
	font-family: Roboto,Helvetica Neue,sans-serif;
}

.limitations-dialog__panel {
	overflow-y: auto;
	overflow-x: hidden;
	margin-bottom: 20px;
}

.limitations-dialog__button-group {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

.limitations-dialog__button-group button {
	border-radius: 8px;
}
</style>
