<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="selection-dialog-wrapper">
		<div class="selection-dialog-pane">
			<div class="selection-dialog">
				<div class="selection-dialog-panel">
					<LimitationsSelectedCriteriaCard v-for="(selectedOntology, index) in selectedEditedCriteria"
						:id="index"
						:key="selectedOntology.id"
						:ui-profile="uiProfile"
						:selected-ontology="selectedOntology"
						:is-limitation-edit-feature="true"
						@get-selected-feature-filter="getSelectedFeatureFilter"
						@update-ontology-profile="updateOntologyProfile(index, $event)"
						@update-selected-ontology="updateSelectedOntology(index, $event)" />
				</div>
				<div class="dialog-button">
					<button :disabled="!isFilterComplete" @click="submit">
						SPEICHERN
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
import type { ConceptType, QuantityType, TimeRange, LimitationsSelectedCriteriaModalData } from '../../types/LimitationsSelectedCriteriaModalData.ts'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData.ts'
import type { UiProfile } from '../../types/FeasibilityQueryBuilderData'

interface updatedOntologyData {
	type: string;
	item: ConceptType | QuantityType | TimeRange | undefined;
}

export default Vue.extend({
	name: 'LimitationsCriterionEditorModal',
	components: {
		LimitationsSelectedCriteriaCard,
	},
	props: {
		selectedEditedCriteria: {
			type: Array<OntologyTreeElement>,
			default: null,
		},
		uiProfile: {
			type: Object as PropType<UiProfile>,
			required: true,
		},
		getFilterInfo: {
			type: Function,
			default: () => {},
		},
	},

	data(): LimitationsSelectedCriteriaModalData {
		return {
			filterInfo: [],
			notEmptyProfileName: [],
			isFilterComplete: false,
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
		getNotEmptyProfile(uiProfile: UiProfile): void {
			const profileKeys = Object.keys(uiProfile)
			for (let i = 0; i < profileKeys.length; i++) {
				const key = profileKeys[i]
				if (uiProfile[key].valueDefinition?.optional === false) {
					this.notEmptyProfileName.push(uiProfile[key].name)
				}
			}
		},

		getSelectedFeatureFilter(filteredCriteria: OntologyTreeElement): void {
			const filter = {
				id: filteredCriteria.id,
				display: filteredCriteria.display,
				conceptType: filteredCriteria.conceptType,
				quantityType: filteredCriteria.quantityType,
				timeRange: filteredCriteria.timeRange,
			}
			const index = this.filterInfo.findIndex(item => item.display === filter.display)
			if (index === -1) {
				this.filterInfo.push(filter)
			} else this.filterInfo[index] = filter

			this.checkCompleteFilter()
		},

		checkCompleteFilter(): void {
			const notCompleteFilter = this.filterInfo.filter(item => {
				let notComplete = false
				for (const key in item) {
					if (item[key] && key !== 'display' && key !== 'id') {
						notComplete = item[key].isFilterOptional ? false : !item[key].completeFilter
						if (notComplete) {
							return notComplete
						}
					}
				}
				return notComplete
			})

			if (notCompleteFilter.length > 0) {
				this.isFilterComplete = false
			} else this.isFilterComplete = true
		},

		updateOntologyProfile(index: string | number, profileType: string): void {
			this.$emit('update-edited-criteria', { id: index, type: profileType })
		},

		updateSelectedOntology(index: string | number, item: updatedOntologyData): void {
			this.$emit('update-edited-criteria', { id: index, ...item })
		},

		submit(): void {
			this.$emit('get-filter-info', this.filterInfo)
			this.$emit('dialog-close')
		},

	},
})
</script>

<style scoped>
@media (max-width: 1300px) {
	.selection-dialog-wrapper {
		top: -40%;
	}
	.selection-dialog {
		max-height: 400px;
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
