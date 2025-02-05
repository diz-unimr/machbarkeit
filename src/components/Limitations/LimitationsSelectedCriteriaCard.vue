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
			<button v-if="!isStateEditFilter" class="limitations-card__delete-button" @click="deleteCard($vnode?.data?.attrs?.id)">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>
		<!-- Loop, Falls mehrere Filter vorkommen -->
		<FilterCard :selected-criterion="selectedCriterion"
			@get-selected-filters="getSelectedFilters" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import FilterCard from './FilterCard.vue'
import type { Criterion } from '../../types/OntologySearchTreeModalData.ts'
import type { ConceptType } from '../../types/ConceptOptionsData.ts'
import type { QuantityType } from '../../types/QuantityOptionsData'
import type { TimeRangeType } from '../../types/TimeRangeOptionsData'

interface LimitationsSelectedCriteriaCardData {
    filterInfo: Criterion | null,
    imgDelete: string,
}

export default Vue.extend({
	name: 'LimitationsSelectedCriteriaCard',
	components: {
		FilterCard,
	},
	props: {
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			default: undefined,
		},
		isStateEditFilter: {
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
			filterInfo: null,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete.png',
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
		// This function is called every time when user click/update a filter
		getSelectedFilters(selectedFiltersInfo: ConceptType | QuantityType | TimeRangeType | undefined, isFilterComplete: boolean = true): void {
			this.$emit('get-selected-criteria-filter', { selectedFiltersInfo, isFilterComplete })
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
	column-gap: 10px;
	align-items: center;
	justify-content:space-between;
	margin: 0px 10px 20px 10px;
}

.limitations-card__title {
	font-weight: 500;
	max-width: 80%;
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
	max-width: 20%;
}

button {
	border-radius: 8px;
}

img {
	width: 25px;
	height: 20px;
}
</style>
