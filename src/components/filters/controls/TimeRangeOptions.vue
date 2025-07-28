<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container">
		<div class="content-option__body">
			<div class="content-option-wrapper">
				<select v-model="selectedValue.type">
					<option value="no filter">
						kein Filter
					</option>
					<option value="at">
						am
					</option>
					<option value="before">
						vor
					</option>
					<option value="after">
						nach
					</option>
					<option value="between">
						zwischen
					</option>
				</select>
				<!-- show different input filter depend on timeRangeRestriction.type -->
				<div v-if="selectedValue.type === 'at'"
					class="text-floating-wrapper">
					<input id="atDate"
						v-model="selectedValue.atDate"
						type="date"
						name="atDate">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'before'"
					class="text-floating-wrapper">
					<input id="beforeDate"
						v-model="selectedValue.beforeDate"
						type="date"
						name="beforeDate">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'after'"
					class="text-floating-wrapper">
					<input id="afterDate"
						v-model="selectedValue.afterDate"
						type="date"
						name="afterDate">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'between'"
					class="input-wrapper">
					<div class="text-floating-wrapper">
						<!-- from date -->
						<input id="afterDate"
							v-model="selectedValue.afterDate"
							type="date"
							name="afterDate">
						<label class="text-floating">von</label>
					</div>
					<div class="text-floating-wrapper">
						<!-- to date -->
						<input id="beforeDate"
							v-model="selectedValue.beforeDate"
							type="date"
							name="beforeDate">
						<label class="text-floating">bis</label>
					</div>
				</div>
			</div>
			<button v-if="label === 'master filter'" :disabled="selectedValue?.type === 'no filter' || !(isDateInputValid && isFilterComplete)" @click="setAllDateChanged">
				DATUM ERSETZEN
			</button>
		</div>
		<div v-if="!isFilterComplete">
			<label class="text-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { TimeRangeOptionsData, TimeRangeType, TimeRangeFilter } from '../../../types/TimeRangeOptionsData'
import type { Criterion } from '../../../types/OntologyPanelData'

export default Vue.extend({
	name: 'TimeRangeOptions',
	props: {
		label: {
			type: String,
			default: 'none master filter',
		},
		index: {
			type: Number,
			default: undefined,
		},
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		timeRestrictionData: {
			type: Object as PropType<TimeRangeFilter>,
			default: undefined,
		},
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			default: undefined,
		},
	},

	data(): TimeRangeOptionsData {
		const timeRestriction = this.selectedCriterion?.timeRestriction as TimeRangeType['timeRestriction']
		return {
			isDateInputValid: false,
			isFilterComplete: true,
			selectedValue: {
				type: timeRestriction && Object.keys(timeRestriction).length > 0
					? timeRestriction.afterDate && timeRestriction.beforeDate && timeRestriction.afterDate.length > 0 && timeRestriction.beforeDate.length > 0 && new Date(timeRestriction.afterDate as string).getTime() === new Date(timeRestriction.beforeDate as string).getTime()
						? 'at'
						: new Date(timeRestriction.afterDate as string).getTime() < new Date(timeRestriction.beforeDate as string).getTime()
							? 'between'
							: timeRestriction.afterDate && !timeRestriction.beforeDate
								? 'after'
								: 'before'
					: 'no filter',
				beforeDate: timeRestriction?.beforeDate
					?? '',
				afterDate: timeRestriction?.afterDate
					?? '',
				atDate: timeRestriction?.beforeDate && timeRestriction?.afterDate
					? timeRestriction?.beforeDate
					: '',
			},
		}
	},

	computed: {},

	watch: {
		timeRestrictionData: {
			handler(newValue) {
				this.selectedValue = { ...newValue }
			},
			deep: true,
		},

		selectedValue: {
			handler(newValue) {
				this.isDateInputValid = this.checkDateInputValue(newValue)
				this.isFilterComplete = this.checkCompleteValue(newValue)
				const timeRangeData = this.handleDateChange(newValue)
				const hasFilterValue = !(newValue.type === 'no filter')
				if (this.selectedCriterion) {
					if (!this.isFilterComplete) {
						this.$emit('handle-filter-change', {
							index: this.index,
							id: this.selectedCriterion.id,
							value: {},
							type: this.selectedCriterion.filterType,
							isFilterComplete: this.isFilterComplete,
							hasFilterValue,
						})
					} else if (timeRangeData) {
						this.$emit('handle-filter-change', {
							index: this.index,
							id: this.selectedCriterion.id,
							value: timeRangeData,
							type: this.selectedCriterion.filterType,
							isFilterComplete: this.isFilterComplete,
							hasFilterValue,
						})
					}
				}
			},
			deep: true,
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.selectedValue.type !== 'no filter') {
				this.selectedValue.type = 'no filter'
				this.selectedValue.afterDate = ''
				this.selectedValue.beforeDate = ''
			}
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
		checkDateInputValue(timeRangeData: TimeRangeOptionsData['selectedValue']) {
			const isValidDate = (d: Date) => !isNaN(d.getTime())
			const at = new Date(timeRangeData.atDate)
			const after = new Date(timeRangeData.afterDate)
			const before = new Date(timeRangeData.beforeDate)

			const isValidRange = timeRangeData.type === 'between'
				&& (isValidDate(after) && isValidDate(before)
				&& after.getTime() < before.getTime())

			const isValidSingle = timeRangeData.type !== 'between' && (isValidDate(at) || isValidDate(after) || isValidDate(before))
			return isValidRange || isValidSingle
		},

		checkCompleteValue(timeRangeData: TimeRangeOptionsData['selectedValue']) {
			const after = new Date(timeRangeData.afterDate)
			const before = new Date(timeRangeData.beforeDate)

			if (timeRangeData.type === 'between' && ((isNaN(after.getTime()) || isNaN(before.getTime())) || after.getTime() >= before.getTime())) {
				return false
			} else {
				return true
			}
		},

		handleDateChange(timeRangeData: TimeRangeOptionsData['selectedValue']): TimeRangeType['timeRestriction'] | null {
			switch (timeRangeData.type) {
			case 'at':
				if (timeRangeData.atDate.length > 0) {
					return {
						afterDate: timeRangeData.atDate,
						beforeDate: timeRangeData.atDate,
					}
				}
				break
			case 'before':
				if (timeRangeData.beforeDate.length > 0) {
					return {
						beforeDate: timeRangeData.beforeDate,
					}
				}
				break
			case 'after':
				if (timeRangeData.afterDate.length > 0) {
					return {
						afterDate: timeRangeData.afterDate,
					}
				}
				break
			case 'between':
				if (timeRangeData.afterDate.length > 0 && timeRangeData.beforeDate.length > 0) {
					return {
						afterDate: timeRangeData.afterDate,
						beforeDate: timeRangeData.beforeDate,
					}
				}
				break
			case 'no filter':
				return {}
			}
			return {}
		},

		setAllDateChanged() {
			this.label === 'master filter' && this.isFilterComplete && this.$emit('set-all-filter', this.selectedValue)
		},
		/* formatDate(event: Event): void {
			const eventTarget = event.target as HTMLInputElement
			const date = new Date(eventTarget.value)
			const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate()
			const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1)
			const year = date.getFullYear()
			const formattedDate = year + '-' + month + '-' + day
			const AttributeName = eventTarget.name
			this.selectedValue[AttributeName.replace('Date', '') + 'Date'] = formattedDate
		}, */
	},
})
</script>

<style scoped>
.content-option-container {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
	width: 100%;
}

.content-option__body {
	display: flex;
	justify-content: space-between;
	align-items: center;
	overflow-y: auto;
	max-height: 150px;
	column-gap: 20px;
}

.content-option-wrapper {
	display: flex;
	justify-content: space-between;
	align-items: center;
	column-gap: 30px;
	margin: 10px 0px;
}

.input-wrapper {
	display: flex;
	column-gap: 10px;
	align-items: center;
}

.content-header__option {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	margin: 10px 0;
}

.text-alert {
	font-weight: 500;
	color: red;
}

.text-floating-wrapper {
	position: relative;
}

.text-floating {
	position: absolute;
	left: 0;
	top: -20%;
	margin-left: 15px;
	padding: 0px 5px;
	background-color: white;
	font-size: 12px;
	cursor: unset;
}

select, input {
	border: 1px solid gray;
	border-radius: 5px;
	height: 45px !important;
	width: 150px !important;
	margin: 3px !important;
	background-color: unset !important;
}

button {
	border-radius: 8px;
	padding: 8px 10px;
	margin: 0px, 20px;
	height: fit-content;
}
</style>
