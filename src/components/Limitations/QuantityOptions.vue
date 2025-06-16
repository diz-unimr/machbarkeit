<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container dialog-border">
		<div class="content-option__text">
			<!-- <span>*</span> --> Geben Sie einen Wertebereich ein:
		</div>
		<div class="content-option__body">
			<div class="content-option-wrapper">
				<select v-model="selectedValue.comparator">
					<option value="no filter">
						kein Filter
					</option>
					<option value="eq">
						gleich
					</option>
					<option value="lt">
						kleiner
					</option>
					<option value="gt">
						größer
					</option>
					<option value="between">
						zwischen
					</option>
				</select>
				<div class="input-wrapper">
					<div v-if="selectedValue.comparator === 'eq' || selectedValue.comparator === 'lt' || selectedValue.comparator === 'gt'"
						class="text-floating-wrapper">
						<input v-model.number="selectedValue.value"
							type="number"
							@change="checkEmptyValue($event, 'value')">
						<label class="text-floating">Wert</label>
					</div>
					<div v-if="selectedValue.comparator === 'between'"
						class="input-wrapper input-wrapper--between">
						<div class="text-floating-wrapper">
							<input v-model.number="selectedValue.minValue"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'minValue')">
							<label class="text-floating">Min</label>
						</div>
						<span class="font-bold">und</span>
						<div class="text-floating-wrapper">
							<input v-model.number="selectedValue.maxValue"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'maxValue')">
							<label class="text-floating">Max</label>
						</div>
					</div>
					<!-- Unit will be shown if type != 'kein Filter' -->
					<div v-if="selectedValue.comparator !== 'no filter'"
						class="text-floating-wrapper">
						<select v-model="selectedValue.unit">
							<option v-for="(unit, index) in newSelectedCriterion.filterOptions"
								:key="index"
								:value="unit">
								{{ unit.display }}
							</option>
						</select>
						<label class="text-floating">Einheit</label>
					</div>
				</div>
			</div>
		</div>
		<div v-if="checkCompleteValue()">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { QuantityOptionsData, QuantityType } from '../../types/QuantityOptionsData.ts'
import type { Criterion } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'QuantityOptions',
	components: {},
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
	data(): QuantityOptionsData {
		return {
			newSelectedCriterion: this.selectedCriterion,
			isFilterComplete: true,
			selectedValue: {
				comparator: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.comparator
					?? ((this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.type === 'quantity-range'
						? 'between'
						: 'no filter'),
				unit: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.unit
					?? this.selectedCriterion.filterOptions![0],
				value: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.value
					?? 0,
				minValue: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.minValue
					?? 0,
				maxValue: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.maxValue
					?? 0,
				type: (this.selectedCriterion.selectedFilter as QuantityType)?.valueFilter?.type
					?? '',
			},
		}
	},

	watch: {
		selectedValue: {
			handler() {
				if (this.selectedValue?.comparator === 'no filter') {
					this.$emit('toggle-reset-button', false)
				} else {
					this.$emit('toggle-reset-button', true)
					if (this.selectedValue.comparator === 'between') {
						this.checkCompleteValue()
						this.quantityType = {
							valueFilter: {
								unit: this.selectedValue.unit,
								minValue: Number(this.selectedValue.minValue),
								maxValue: Number(this.selectedValue.maxValue),
								type: 'quantity-range',
							},
						}
					} else if (this.selectedValue.comparator === 'eq' || this.selectedValue.comparator === 'lt' || this.selectedValue.comparator === 'gt') {
						this.quantityType = {
							valueFilter: {
								unit: this.selectedValue.unit,
								comparator: this.selectedValue.comparator,
								value: Number(this.selectedValue.value),
								type: 'quantity-comparator',
							},
						}
					}
				}
				this.$emit('get-selected-filter-option', this.quantityType, this.isFilterComplete)
			},
			deep: true,
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.selectedValue.comparator !== 'no filter') {
				this.selectedValue.comparator = 'no filter'
				this.selectedValue.unit = this.selectedCriterion.filterOptions![0]
				this.selectedValue.value = 0
				this.selectedValue.minValue = 0
				this.selectedValue.maxValue = 0
			}
		},

	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (typeof this.newSelectedCriterion.context === 'string') {
			this.newSelectedCriterion.context = JSON.parse(this.newSelectedCriterion.context)
		}
		if (typeof this.newSelectedCriterion.termCodes === 'string') {
			this.newSelectedCriterion.termCodes = JSON.parse(this.newSelectedCriterion.termCodes)
		}
		if (typeof this.newSelectedCriterion.filterOptions === 'string') {
			this.newSelectedCriterion.filterOptions = JSON.parse(this.newSelectedCriterion.filterOptions)
		}
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},

	methods: {
		getQuantityOption() {
			this.selectedValue.unit = (this.selectedCriterion.selectedFilter as QuantityType).valueFilter?.unit ?? this.selectedCriterion.filterOptions![0]
			this.selectedValue.comparator = (this.selectedCriterion.selectedFilter as QuantityType).valueFilter?.comparator ?? 'no filter'
		},

		checkEmptyValue(event: Event, tag: string): void {
			const eventTarget = event.target as HTMLInputElement
			if (eventTarget.value.length === 0) {
				this.selectedValue[tag] = 0
			}
		},

		checkCompleteValue() {
			if (this.selectedValue.comparator === 'between' && (this.selectedValue.minValue >= this.selectedValue.maxValue)) {
				this.isFilterComplete = false
				return true
			} else {
				this.isFilterComplete = true
				return false
			}
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

.dialog-border {
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 10px 20px;
	margin-bottom: 20px;
}

.content-option__text {
	font-weight: 500;
}

.content-option__text span {
	color: red;
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

.input-wrapper--between {
	column-gap: 10px !important;
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
}

.content-option-alert {
	font-weight: 500;
	color: red;
}

.font-bold {
	font-weight: 500;
}

select, input {
	border: 1px solid gray;
	border-radius: 5px;
	height: 45px !important;
	width: 150px !important;
	margin: 3px !important;
	background-color: unset !important;
}
</style>
