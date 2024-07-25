<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="limitations-card">
		<div class="limitations-card__header">
			<p class="limitations-card__title">
				{{ selectedCriterion.display }}
			</p>
			<button v-if="!isEditFilterState" class="limitations-card__delete-button" @click="deleteCard($vnode?.data?.attrs?.id)">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>
		<template v-if="uiProfile != null || uiProfile != undefined">
			<template v-for="(attribute, index) in ['timeRestrictionAllowed', 'valueDefinition']">
				<template v-if="attribute === 'timeRestrictionAllowed' && profile?.timeRestrictionAllowed">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Zeitraum"
						attribute="timeRestrictionAllowed"
						:selected-criterion="selectedCriterion"
						@get-selected-filters="getSelectedFilters" />
				</template>
				<template v-if="attribute === 'valueDefinition' && profile?.valueDefinition?.type">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Wertebereich"
						attribute="valueDefinition"
						:selected-criterion="selectedCriterion"
						:is-filter-optional="profile.valueDefinition.optional"
						@get-selected-filters="getSelectedFilters" />
				</template>
			</template>
		</template>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import FilterCard from './FilterCard.vue'
import type { UiProfile } from '../../types/FeasibilityQueryBuilderData.ts'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData.ts'
import type { LimitationsSelectedCriteriaCardData } from '../../types/LimitationsSelectedCriteriaCardData.ts'
import type { ConceptType } from '../../types/ConceptOptionsData.ts'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRange } from '../../types/TimeRangeOptionsData'

export default Vue.extend({
	name: 'LimitationsSelectedCriteriaCard',
	components: {
		FilterCard,
	},
	props: {
		uiProfile: {
			type: Object as PropType<UiProfile>,
			required: true,
		},
		selectedCriterion: {
			type: Object as PropType<OntologyTreeElement>,
			default: undefined,
		},
		isEditFilterState: {
			type: Boolean,
			default: false,
		},
		getSelectedCriteriaFilter: {
			type: Function,
			default: () => {},
		},
		deleteDialogCard: {
			type: Function,
			default: () => {},
		},
	},
	data(): LimitationsSelectedCriteriaCardData {
		return {
			profile: null,
			filterInfo: null,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete.png',
		}
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getProfile()
		this.createFilterInfo()
	},
	beforeMount() {},
	mounted() {
		this.$emit('get-selected-criteria-filter', { status: 'initial', item: this.filterInfo })
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		getProfile(): void {
			if (this.selectedCriterion.context.display === 'Diagnose' || this.selectedCriterion.context.display === 'Prozedur') {
				this.profile = this.uiProfile[this.selectedCriterion.context.display]
			} else {
				this.profile = this.uiProfile[this.selectedCriterion.display]
			}
		},

		createFilterInfo() {
			this.filterInfo = {
				id: this.selectedCriterion.id,
				display: this.selectedCriterion.display,
				context: this.selectedCriterion.context,
				conceptType: null,
				quantityType: null,
				timeRange: null,
			}
		},

		// This function is called every time when user click/update a filter
		getSelectedFilters(status: string, selectedFiltersInfo: ConceptType | QuantityType | TimeRange): void {
			if (this.filterInfo?.display === selectedFiltersInfo.display) {
				switch (selectedFiltersInfo.type) {
				case 'conceptType':
					this.filterInfo.conceptType = selectedFiltersInfo as ConceptType
					status === 'update' && this.$emit('get-selected-criteria-filter', { status, item: this.filterInfo })
					break
				case 'quantityType':
					this.filterInfo.quantityType = selectedFiltersInfo as QuantityType
					status === 'update' && this.$emit('get-selected-criteria-filter', { status, item: this.filterInfo })
					break
				case 'timeRange':
					this.filterInfo.timeRange = selectedFiltersInfo as TimeRange
					status === 'update' && this.$emit('get-selected-criteria-filter', { status, item: this.filterInfo })
					break
				}
			}
		},

		deleteCard(key: number): void {
			this.$emit('delete-dialog-card', key)
		},
	},
})
</script>

<style scoped>
.limitations-card {
	display: flex;
	flex-direction: column;
	place-content: center space-around;
	padding: 20px;
	margin: 20px;
	box-shadow: 0 3px 3px -2px #0003, 0 3px 4px #00000024, 0 1px 8px #0000001f;
	border-radius: 4px;
}

.limitations-card__header {
	display: flex;
	column-gap: 10%;
	align-items: center;
	justify-content:space-between;
	margin: 0px 10px 20px 10px;
}

.limitations-card__title {
	font-weight: 500;
}

.limitations-card__delete-button {
	display: flex;
	column-gap: 5px;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
	background-color: white;
	align-items: center;
}

button {
	border-radius: 8px;
}

img {
	width: 25px;
	height: 20px;
}
</style>
