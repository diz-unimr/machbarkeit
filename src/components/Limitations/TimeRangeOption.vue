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
import Vue from 'vue'

interface TimeRangOptaionData {
    timeRangeRestriction: {
        type: string,
        fromDate: string | null,
        fromDateFormatted: string | null,
        toDate: string | null,
        toDateFormatted: string | null,
    },
    isFilterOptional: boolean
}

export default Vue.extend({
	name: 'TimeRangeOption',
	props: {
		profile: {
			type: Object,
			default: Object,
		},
		toggleResetButton: {
			type: Function,
			default: () => {},
		},
		getSelectedOption: {
			type: Function,
			default: () => {},
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
	},
	data(): TimeRangOptaionData {
		return {
			timeRangeRestriction: {
				type: 'kein Filter',
				fromDate: null,
				fromDateFormatted: null,
				toDate: null,
				toDateFormatted: null,
			},
			isFilterOptional: this.profile.valueDefinition?.optional === undefined ? true : this.profile.valueDefinition?.optional,
		}
	},

	watch: {
		timeRangeRestriction: {
			handler() {
				if (this.timeRangeRestriction.type === 'kein Filter') {
					this.$emit('get-selected-option', {
						type: 'timeRange',
						value: {},
						isFilterOptional: this.isFilterOptional,
						completeFilter: false,
					})
				} else if (this.timeRangeRestriction.type === 'zwischen') {
					if (!!this.timeRangeRestriction.fromDate && !!this.timeRangeRestriction.toDate) {
						const validDate = !(this.timeRangeRestriction.fromDate > this.timeRangeRestriction.toDate)
						this.$emit('get-selected-option', {
							type: 'timeRange',
							value: this.timeRangeRestriction,
							isFilterOptional: validDate,
							completeFilter: validDate,
						})
					}
				} else {
					if (this.timeRangeRestriction.fromDate) {
						this.$emit('get-selected-option', {
							type: 'timeRange',
							value: this.timeRangeRestriction,
							isFilterOptional: this.isFilterOptional,
							completeFilter: this.timeRangeRestriction.type.length > 0 && this.timeRangeRestriction.fromDate?.length > 0,
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

	created() {
		this.$emit('get-selected-option', {
			type: 'timeRange',
			value: {},
			isFilterOptional: this.isFilterOptional,
			completeFilter: false,
		})
	},

	methods: {
		addTimeRangeOption() {
		},

		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		formatDate(event: any) {
			const date = new Date(event.target.value)
			const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate()
			const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1)
			const year = date.getFullYear()
			const formattedDate = day + '-' + month + '-' + year
			this.timeRangeRestriction[event.target.name + 'Formatted'] = formattedDate
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
