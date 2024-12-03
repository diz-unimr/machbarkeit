<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container">
		<div class="content-option__body">
			<div class="content-option-wrapper">
				<select v-model="selectedValue.type">
					<option value="kein filter">
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
				<!-- show different input filter depend on timeRangeRestriction.type -->
				<div v-if="selectedValue.type === 'am'"
					class="text-floating-wrapper">
					<input id="atDate"
						v-model="selectedValue.atDate"
						type="date"
						name="atDate"
						@input="formatDate($event)">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'vor'"
					class="text-floating-wrapper">
					<input id="beforeDate"
						v-model="selectedValue.atDate"
						type="date"
						name="beforeDate"
						@input="formatDate($event)">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'nach'"
					class="text-floating-wrapper">
					<input id="afterDate"
						v-model="selectedValue.atDate"
						type="date"
						name="afterDate"
						@input="formatDate($event)">
					<label class="text-floating">{{ selectedValue.type }}</label>
				</div>
				<div v-if="selectedValue.type === 'zwischen'"
					class="input-wrapper">
					<div class="text-floating-wrapper">
						<!-- from date -->
						<input id="afterDate"
							v-model="selectedValue.afterDate"
							type="date"
							name="afterDate"
							@input="formatDate($event)">
						<label class="text-floating">von</label>
					</div>
					<div class="text-floating-wrapper">
						<!-- to date -->
						<input id="beforeDate"
							v-model="selectedValue.beforeDate"
							type="date"
							name="beforeDate"
							@input="formatDate($event)">
						<label class="text-floating">bis</label>
					</div>
				</div>
			</div>
		</div>
		<div v-if="selectedValue.type === 'zwischen' && ((selectedValue.afterDate ?? '') >= (selectedValue.beforeDate ?? ''))">
			<label class="text-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { TimeRangeOptionsData, TimeRangeType } from '../../types/TimeRangeOptionsData'
import type { Criterion } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'TimeRangeOptions',
	props: {
		isResetDisabled: {
			type: Boolean,
			default: true,
		},
		selectedCriterion: {
			type: Object as PropType<Criterion>,
			required: true,
		},
	},

	data(): TimeRangeOptionsData {
		return {
			isFilterComplete: true,
			selectedValue: {
				type: (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction
					? (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate && (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate && new Date((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate as string).getTime() === new Date((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate as string).getTime()
						? 'am'
						: new Date((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate as string) < new Date((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate as string)
							? 'zwischen'
							: (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate && !(this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate
								? 'nach'
								: 'vor'
					: 'kein filter',
				beforeDate: (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate
					?? ((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate ?? ''),
				afterDate: (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate
					?? ((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate ?? ''),
				atDate: (this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.afterDate as string
					?? ((this.selectedCriterion.selectedFilter as TimeRangeType)?.timeRestriction?.beforeDate as string ?? ''),
			},
		}
	},

	watch: {
		selectedValue: {
			handler() {
				this.isFilterComplete = true
				if (this.selectedValue.type === 'kein filter') {
					this.$emit('toggle-reset-button', false)
				} else {
					this.$emit('toggle-reset-button', true)
					if (this.selectedValue.type === 'am' && this.selectedValue.atDate) {
						this.timeRangeType = {
							timeRestriction: {
								afterDate: this.selectedValue.atDate,
								beforeDate: this.selectedValue.atDate,
							},
						}
					} else if (this.selectedValue.type === 'vor' && this.selectedValue.atDate) {
						this.timeRangeType = {
							timeRestriction: {
								beforeDate: this.selectedValue.atDate,
							},
						}
					} else if (this.selectedValue.type === 'nach' && this.selectedValue.atDate) {
						this.timeRangeType = {
							timeRestriction: {
								afterDate: this.selectedValue.atDate,
							},
						}
					} else if (this.selectedValue.type === 'zwischen') {
						if (new Date(this.selectedValue.afterDate) < new Date(this.selectedValue.beforeDate)) {
							this.timeRangeType = {
								timeRestriction: {
									afterDate: this.selectedValue.afterDate,
									beforeDate: this.selectedValue.beforeDate,
								},
							}
							this.isFilterComplete = true
						} else this.isFilterComplete = false
					}
				}
				this.$emit('get-selected-filter-option', this.timeRangeType, this.isFilterComplete)
			},
			deep: true,
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.selectedValue.type !== 'kein filter') {
				this.selectedValue.type = 'kein filter'
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
		formatDate(event: Event): void {
			const eventTarget = event.target as HTMLInputElement
			const date = new Date(eventTarget.value)
			const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate()
			const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1)
			const year = date.getFullYear()
			const formattedDate = year + '-' + month + '-' + day
			const AttributeName = eventTarget.name
			this.selectedValue[AttributeName.replace('Date', '') + 'Date'] = formattedDate
		},
	},
})
</script>

<style scoped>
.content-option-container {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
}

.content-option__body {
	overflow-y: auto;
	max-height: 150px;
}

.content-option-wrapper {
	display: flex;
	align-items: center;
	column-gap: 50px;
	margin: 20px 0px;
}

.input-wrapper {
	display: flex;
	column-gap: 20px;
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
}
</style>
