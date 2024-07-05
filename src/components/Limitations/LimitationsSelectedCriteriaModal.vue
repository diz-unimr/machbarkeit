<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="selection-dialog-wrapper">
		<div class="selection-dialog-pane">
			<div class="selection-dialog">
				<h2 class="selection-dialog-title">
					Einschränkungen der ausgewählten Merkmale
				</h2>
				<div class="selection-dialog-panel">
					<LimitationsSelectedCriteriaCard v-for="(selectedCriterion, index) in selectedCriteria"
						:id="index"
						:key="selectedCriterion.id"
						:ui-profile="uiProfile"
						:selected-criterion="selectedCriterion"
						:is-edit-filter-state="isEditFilterState"
						@get-selected-criteria-filter="getSelectedCriteriaFilter(index, $event)"
						@delete-dialog-card="deleteDialogCard" />
				</div>
				<div class="dialog-button">
					<button :disabled="!isFilterComplete" @click="submit">
						<template v-if="isEditFilterState">
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
import Vue, { type PropType } from 'vue'
import LimitationsSelectedCriteriaCard from './LimitationsSelectedCriteriaCard.vue'
import type { LimitationsSelectedCriteriaModalData } from '../../types/LimitationsSelectedCriteriaModalData.ts'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData.ts'
import type { UiProfile } from '../../types/FeasibilityQueryBuilderData'
import type { FilterInfo } from '../../types/LimitationsSelectedCriteriaCardData'
import type { TimeRange } from '../../types/TimeRangeOptionsData'

interface SelectedCriteriaFilter {
	status: string;
	item: FilterInfo;
}

export default Vue.extend({
	name: 'LimitationsSelectedCriteriaModal',
	components: {
		LimitationsSelectedCriteriaCard,
	},
	props: {
		selectedCriteria: {
			type: Array<OntologyTreeElement>,
			required: true,
		},
		uiProfile: {
			type: Object as PropType<UiProfile>,
			required: true,
		},
		isEditFilterState: {
			type: Boolean,
			default: false,
		},
	},

	data(): LimitationsSelectedCriteriaModalData {
		return {
			filterInfo: [],
			selectedCriteriaFiltersInfo: [],
			isFilterComplete: false,
		}
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {},
	beforeMount() {},
	mounted() {
		this.checkCompleteFilter(this.selectedCriteriaFiltersInfo)
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		deleteDialogCard(index: number): void {
			if (this.selectedCriteria !== null) {
				this.$emit('delete-selected-criteria', index)
				this.selectedCriteriaFiltersInfo.splice(index, 1)
				if (this.selectedCriteria.length === 0) {
					this.$emit('dialog-close')
				} else this.checkCompleteFilter(this.selectedCriteriaFiltersInfo)
			}
		},

		checkCompleteFilter(filtersInfo: FilterInfo[]): void {
			const notCompleteFilter = filtersInfo.filter(item => {
				let notComplete = false

				item.timeRange && item.timeRange?.isFilterComplete === false && (notComplete = true)
				item.quantityType?.isFilterComplete === false && (notComplete = true)
				item.conceptType?.isFilterComplete === false && (notComplete = true)

				return notComplete
			})

			notCompleteFilter.length > 0 ? (this.isFilterComplete = false) : (this.isFilterComplete = true)
			/* if (notCompleteFilter.length > 0) {
				this.isFilterComplete = false
			} else this.isFilterComplete = true */
		},

		getSelectedCriteriaFilter(index: string | number, data: SelectedCriteriaFilter): void {
			if (this.selectedCriteria[index].display === data.item.display) {
				this.selectedCriteriaFiltersInfo[index] = data.item
			}

			data.status === 'update' && this.checkCompleteFilter(this.selectedCriteriaFiltersInfo)
		},

		checkCompleteFilterInput(items: FilterInfo[]) {
			for (const item of items) {
				item.timeRange?.value.type && !item.timeRange?.value.fromDate && (item.timeRange.value = {} as TimeRange['value'])
			}
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
	.selection-dialog-wrapper {
		top: -55%;
	}
	.selection-dialog {
		max-height: 480px;
	}
}

.selection-dialog-wrapper {
	display: flex;
	position: relative;
	z-index: 100;
	width: 85%;
	/* top: 20%; */
	margin: 0px auto 0px auto;
}

.selection-dialog-pane {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
	max-height: 850px;
}

.selection-dialog {
	display: flex;
	flex-direction: column;
	width: 100%;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.selection-dialog-title {
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

.selection-dialog-panel {
	overflow-y: auto;
	overflow-x: hidden;
	margin-bottom: 20px;
}

.dialog-button {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

.dialog-button button {
	border-radius: 8px;
}
</style>
