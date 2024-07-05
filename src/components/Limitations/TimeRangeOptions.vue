<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option dialog-card">
		<div class="content-option__body">
			<div class="content-option-dropdown">
				<select v-model="timeRangeRestriction.type"
					class="input-selection-textbox">
					<option value="kein Filter">
						kein Filter
					</option>
					<option value="zwischen">
						zwischen
					</option>
					<option value="am">
						am
					</option>
					<option value="vor">
						vor
					</option>
					<option value="nach">
						nach
					</option>
				</select>
				<div class="flex-row value-unit">
					<div v-if="timeRangeRestriction.type === 'am' || timeRangeRestriction.type === 'vor' || timeRangeRestriction.type === 'nach'"
						class="input-wrapper">
						<input id="fromDate"
							v-model="timeRangeRestriction.fromDate"
							class="input-selection-textbox"
							type="date"
							name="fromDate"
							@input="formatDate($event)">
						<label class="value-floating">{{ timeRangeRestriction.type }}</label>
					</div>
					<div v-if="timeRangeRestriction.type === 'zwischen'"
						class="flex-row">
						<div class="input-wrapper">
							<input id="fromDate"
								v-model="timeRangeRestriction.fromDate"
								class="input-selection-textbox"
								type="date"
								name="fromDate"
								@input="formatDate($event)">
							<label class="value-floating">von</label>
						</div>
						<div class="input-wrapper">
							<input id="toDate"
								v-model="timeRangeRestriction.toDate"
								class="input-selection-textbox"
								type="date"
								name="toDate"
								@input="formatDate($event)">
							<label class="value-floating">bis</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="timeRangeRestriction.type === 'zwischen' && ((timeRangeRestriction.fromDate ? timeRangeRestriction.fromDate : '') > (timeRangeRestriction.toDate ? timeRangeRestriction.toDate : ''))">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { TimeRangeOptionsData } from '../../types/TimeRangeOptionsData'
import type { Profile } from '../../types/FeasibilityQueryBuilderData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'TimeRangeOptions',
	props: {
		profile: {
			type: Object as PropType<Profile>,
			required: true,
		},
		toggleResetButton: {
			type: Function,
			default: () => {},
		},
		getSelectedFilterOption: {
			type: Function,
			default: () => {},
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		selectedCriterion: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
	},

	data(): TimeRangeOptionsData {
		return {
			timeRangeRestriction: {
				type: this.selectedCriterion.timeRange?.value.type
					? this.selectedCriterion.timeRange.value.type
					: 'kein Filter',
				fromDate: this.selectedCriterion.timeRange?.value.fromDate
					? this.selectedCriterion.timeRange.value.fromDate
					: null,
				fromDateFormatted: this.selectedCriterion.timeRange?.value.fromDateFormatted
					? this.selectedCriterion.timeRange.value.fromDateFormatted
					: null,
				toDate: this.selectedCriterion.timeRange?.value.toDate
					? this.selectedCriterion.timeRange.value.toDate
					: null,
				toDateFormatted: this.selectedCriterion.timeRange?.value.toDateFormatted
					? this.selectedCriterion.timeRange.value.toDateFormatted
					: null,
			},
		}
	},

	watch: {
		timeRangeRestriction: {
			handler() {
				if (this.timeRangeRestriction.type === 'kein Filter') {
					this.$emit('get-selected-filter-option', 'update', {
						type: 'timeRange',
						display: this.selectedCriterion.display,
						isFilterOptional: this.profile.valueDefinition?.optional === undefined ? true : this.profile.valueDefinition?.optional,
						isFilterComplete: true,
						value: {},
					})
				} else if (this.timeRangeRestriction.type === 'zwischen') {
					if (!!this.timeRangeRestriction.fromDate && !!this.timeRangeRestriction.toDate) {
						const validDate = !(this.timeRangeRestriction.fromDate > this.timeRangeRestriction.toDate)
						this.$emit('get-selected-filter-option', 'update', {
							type: 'timeRange',
							display: this.selectedCriterion.display,
							isFilterOptional: validDate,
							isFilterComplete: validDate,
							value: this.timeRangeRestriction,
						})
					}
				} else {
					if (this.timeRangeRestriction.fromDate) {
						this.$emit('get-selected-filter-option', 'update', {
							type: 'timeRange',
							display: this.selectedCriterion.display,
							isFilterOptional: this.profile.valueDefinition?.optional === undefined ? true : this.profile.valueDefinition?.optional,
							// isFilterComplete: this.timeRangeRestriction.type.length > 0 && this.timeRangeRestriction.fromDate?.length > 0,
							isFilterComplete: true,
							value: this.timeRangeRestriction,
						})
					}
				}
			},
			deep: true,
		},

		'timeRangeRestriction.type'() {
			if (this.timeRangeRestriction.type !== 'kein Filter') {
				this.$emit('toggle-reset-button', true)
			} else {
				this.$emit('toggle-reset-button', false)
			}
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.timeRangeRestriction.type !== 'kein Filter') {
				this.timeRangeRestriction.type = 'kein Filter'
				this.timeRangeRestriction.fromDate = null
				this.timeRangeRestriction.fromDateFormatted = null
				this.timeRangeRestriction.toDate = null
				this.timeRangeRestriction.toDateFormatted = null
			}
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.$emit('get-selected-filter-option', 'initial', {
			type: 'timeRange',
			display: this.selectedCriterion.display,
			isFilterOptional: this.profile.valueDefinition?.optional === undefined ? true : this.profile.valueDefinition?.optional,
			isFilterComplete: true,
			value: this.selectedCriterion.timeRange?.value
				? this.timeRangeRestriction
				: {},
		})
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},

	methods: {
		formatDate(event: Event): void {
			const eventTarget = event.target as HTMLInputElement
			const date = new Date(eventTarget.value)
			const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate()
			const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1)
			const year = date.getFullYear()
			const formattedDate = day + '-' + month + '-' + year
			this.timeRangeRestriction[eventTarget.name + 'Formatted'] = formattedDate
		},
	},
})
</script>

<style scoped>
select {
	margin: 3px !important;
}

.flex-row {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	align-items: center;
}

.font-bold {
	font-weight: 500;
}

.content-header__option {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	margin: 10px 0;
}

.content-option {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
}

.content-option__body {
	overflow-y: auto;
	max-height: 150px;
}

.content-option-alert {
	font-weight: 500;
	color: red;
}

.content-option-dropdown {
	display: flex;
	flex-direction: row;
	column-gap: 50px;
	margin: 20px 0px;
}

.input-selection-textbox {
	height: 45px !important;
	width: 150px !important;
	border: 1px solid gray;
	border-radius: 5px;
}

.input-wrapper {
	position: relative;
}

.value-floating {
	position: absolute;
	left: 0;
	top: -20%;
	margin-left: 15px;
	padding: 0px 5px;
	background-color: white;
	font-size: 12px;
	cursor: unset;
}
</style>
