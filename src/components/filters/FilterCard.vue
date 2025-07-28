<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="filter-card">
		<div class="filter-card__header">
			<p class="filter-card__title">
				{{ selectedCriterion.display }} ({{ selectedCriterion.termCodes[0].code }})
			</p>
			<button v-if="!isStateEditFilter" class="filter-card__delete-button" @click="deleteCard(index, selectedCriterion.id)">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>
		<!-- Loop, Falls mehrere Filter vorkommen -->
		<FilterSection :selected-criterion="selectedCriterion"
			:index="index"
			:is-state-edit-filter="isStateEditFilter"
			:time-restriction="timeRestriction"
			@update-filter="$emit('update-filter', $event)" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import FilterSection from './FilterSection.vue'
import type { Criterion } from '../../types/OntologyPanelData'
import type { TimeRangeFilter } from '../../types/TimeRangeOptionsData'

interface FilterCardData {
    filterInfo: Criterion | null,
    imgDelete: string,
}

export default Vue.extend({
	name: 'FilterCard',
	components: {
		FilterSection,
	},
	props: {
		index: {
			type: Number,
			required: true,
		},
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			default: undefined,
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
	data(): FilterCardData {
		return {
			filterInfo: null,
			imgDelete: './img/delete.png',
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
		deleteCard(index: number, id: string): void {
			this.$emit('delete-dialog-card', index, id)
		},
	},
})
</script>

<style scoped>
.filter-card {
	display: flex;
	flex-direction: column;
	place-content: center space-around;
	padding: 20px;
	margin: 20px;
	box-shadow: 0 3px 3px -2px #0003, 0 3px 4px #00000024, 0 1px 8px #0000001f;
	border-radius: 4px;
}

.filter-card__header {
	display: flex;
	column-gap: 10px;
	align-items: center;
	justify-content:space-between;
	margin: 0px 10px 20px 10px;
}

.filter-card__title {
	font-weight: 500;
	max-width: 80%;
}

.filter-card__delete-button {
	display: flex;
	column-gap: 5px;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
	background-color: unset !important;
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
