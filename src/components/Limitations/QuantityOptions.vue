<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="content-option-container dialog-border">
		<div class="content-option__text">
			<span>*</span> Geben Sie einen Wertebereich ein:
		</div>
		<div class="content-option__body">
			<div class="content-option-wrapper">
				<select v-model="comparisonRestriction.type">
					<option value="kein Filter">
						kein Filter
					</option>
					<option value="gleich">
						gleich
					</option>
					<option value="kleiner">
						kleiner
					</option>
					<option value="größer">
						größer
					</option>
					<option value="zwischen">
						zwischen
					</option>
				</select>
				<div class="input-wrapper">
					<div v-if="comparisonRestriction.type === 'gleich' || comparisonRestriction.type === 'kleiner' || comparisonRestriction.type === 'größer'"
						class="text-floating-wrapper">
						<input v-model="comparisonRestriction.value"
							type="number"
							@change="checkEmptyValue($event, 'value')">
						<label class="text-floating">Wert</label>
					</div>
					<div v-if="comparisonRestriction.type === 'zwischen'"
						class="input-wrapper input-wrapper--between">
						<div class="text-floating-wrapper">
							<input v-model="comparisonRestriction.min"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'min')">
							<label class="text-floating">Min</label>
						</div>
						<span class="font-bold">und</span>
						<div class="text-floating-wrapper">
							<input v-model="comparisonRestriction.max"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'max')">
							<label class="text-floating">Max</label>
						</div>
					</div>
					<!-- Unit will be shown if type != 'kein Filter' -->
					<div v-if="comparisonRestriction.type !== 'kein Filter'"
						class="text-floating-wrapper">
						<select v-model="comparisonRestriction.unit">
							<option v-for="(unit_display, index) in profile.valueDefinition?.allowedUnits"
								:key="index"
								:value="unit_display.display">
								{{ unit_display.display }}
							</option>
						</select>
						<label class="text-floating">Einheit</label>
					</div>
				</div>
			</div>
		</div>
		<div v-if="comparisonRestriction.type === 'zwischen' && (comparisonRestriction.min >= comparisonRestriction.max)">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { QuantityOptionsData } from '../../types/QuantityOptionsData.ts'
import type { Profile } from '../../types/FeasibilityQueryBuilderData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData'

export default Vue.extend({
	name: 'QuantityOptions',
	components: {},
	props: {
		profile: {
			type: Object as PropType<Profile>,
			required: true,
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
	data(): QuantityOptionsData {
		return {
			typeSymbol: {
				'kein Filter': 'kein Filter',
				gleich: '=',
				kleiner: '<',
				größer: '>',
				zwischen: 'zwischen',
			},
			comparisonRestriction: {
				type: this.selectedCriterion.quantityType?.value.type
					? this.selectedCriterion.quantityType.value.type
					: 'kein Filter',
				typeSymbol: this.selectedCriterion.quantityType?.value.typeSymbol
					? this.selectedCriterion.quantityType.value.typeSymbol
					: 'kein Filter',
				unit: this.selectedCriterion.quantityType?.value.unit
					? this.selectedCriterion.quantityType.value.unit
					: this.profile.valueDefinition?.allowedUnits[0].display,
				value: this.selectedCriterion.quantityType?.value.value
					? this.selectedCriterion.quantityType.value.value
					: '0',
				min: this.selectedCriterion.quantityType?.value.min
					? this.selectedCriterion.quantityType.value.min
					: '0',
				max: this.selectedCriterion.quantityType?.value.max
					? this.selectedCriterion.quantityType.value.max
					: '0',
			},
		}
	},

	watch: {
		'comparisonRestriction.type'(): void {
			this.comparisonRestriction.typeSymbol = this.typeSymbol[this.comparisonRestriction.type]

			if (this.comparisonRestriction.type !== 'kein Filter') {
				this.$emit('toggle-reset-button', true)
			} else {
				this.$emit('toggle-reset-button', false)
			}
		},

		comparisonRestriction: {
			handler() {
				if (this.comparisonRestriction.type === 'kein Filter') {
					this.$emit('get-selected-filter-option', 'update', {
						type: 'quantityType',
						display: this.selectedCriterion.display,
						isFilterOptional: this.profile.valueDefinition?.optional,
						isFilterComplete: this.profile.valueDefinition?.optional,
						value: {},
					})
				} else if (this.comparisonRestriction.type === 'zwischen') {
					this.$emit('get-selected-filter-option', 'update', {
						type: 'quantityType',
						display: this.selectedCriterion.display,
						isFilterOptional: this.profile.valueDefinition?.optional,
						isFilterComplete: this.comparisonRestriction.type && (this.comparisonRestriction.min < this.comparisonRestriction.max),
						value: this.comparisonRestriction,
					})
				} else {
					this.$emit('get-selected-filter-option', 'update', {
						type: 'quantityType',
						display: this.selectedCriterion.display,
						isFilterOptional: this.profile.valueDefinition?.optional,
						isFilterComplete: this.comparisonRestriction.type.length > 0 && this.comparisonRestriction.value.length > 0,
						value: this.comparisonRestriction,
					 })
				}
			},
			deep: true,
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.comparisonRestriction.type !== 'kein Filter') {
				this.comparisonRestriction.type = 'kein Filter'
				this.comparisonRestriction.typeSymbol = 'kein Filter'
				this.comparisonRestriction.unit = this.profile.valueDefinition?.allowedUnits[0].display
				this.comparisonRestriction.value = '0'
				this.comparisonRestriction.min = '0'
				this.comparisonRestriction.max = '0'
			}
		},

	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.$emit('get-selected-filter-option', 'initial', {
			type: 'quantityType',
			display: this.selectedCriterion.display,
			isFilterOptional: this.profile.valueDefinition?.optional,
			isFilterComplete: this.selectedCriterion.quantityType?.isFilterComplete
				? this.selectedCriterion.quantityType?.isFilterComplete
				: this.profile.valueDefinition?.optional,
			value: this.selectedCriterion.quantityType?.value
				? this.comparisonRestriction
				: {},
		})
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},

	methods: {
		checkEmptyValue(event: Event, tag: string): void {
			const eventTarget = event.target as HTMLInputElement
			if (eventTarget.value.length === 0) {
				this.comparisonRestriction[tag] = '0'
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
}
</style>
