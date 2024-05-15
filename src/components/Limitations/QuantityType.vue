<template>
	<div class="content-option dialog-card">
		<div class="content-option__header">
			<span>*</span> Geben Sie einen Wertebereich ein:
		</div>
		<div class="content-option__body">
			<div class="content-option-dropdown">
				<select v-model="comparisonRestriction.type"
					class="input-selection-textbox">
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
				<div class="flex-row value-unit">
					<div v-if="comparisonRestriction.type === 'gleich' || comparisonRestriction.type === 'kleiner' || comparisonRestriction.type === 'größer'"
						class="input-wrapper">
						<input v-model="comparisonRestriction.value"
							class="input-selection-textbox"
							type="number"
							@change="checkEmptyValue($event, 'value')">
						<label class="value-floating">Wert</label>
					</div>
					<div v-if="comparisonRestriction.type === 'zwischen'"
						class="flex-row">
						<div class="input-wrapper">
							<input v-model="comparisonRestriction.min"
								class="input-selection-textbox"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'min')">
							<label class="value-floating">Min</label>
						</div>
						<span class="font-bold">und</span>
						<div class="input-wrapper">
							<input v-model="comparisonRestriction.max"
								class="input-selection-textbox"
								style="width: 100px !important;"
								type="number"
								@change="checkEmptyValue($event, 'max')">
							<label class="value-floating">Max</label>
						</div>
					</div>
					<!-- Unit will be shown if type != 'kein Filter' -->
					<div v-if="comparisonRestriction.type !== 'kein Filter'"
						class="input-wrapper">
						<select v-model="comparisonRestriction.unit"
							class="input-selection-textbox">
							<option v-for="(unit_display, index) in profile.valueDefinition.allowedUnits"
								:key="index"
								:value="unit_display.display">
								{{ unit_display.display }}
							</option>
						</select>
						<label class="value-floating">Einheit</label>
					</div>
				</div>
			</div>
		</div>
		<div v-if="comparisonRestriction.type === 'zwischen' && (comparisonRestriction.min >= comparisonRestriction.max)">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
		<!-- <div v-else-if="comparisonRestriction.type === 'zwischen' && (comparisonRestriction.min.length === 0 || comparisonRestriction.max.length === 0)">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div>
		<div v-else-if="comparisonRestriction.type !== 'zwischen' && comparisonRestriction.value.length === 0">
			<label class="content-option-alert">Der minimale Wert muss kleiner als der maximale Wert sein</label>
		</div> -->
	</div>
</template>

<script>
export default {
	name: 'QuantityType',
	components: {},
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
	data() {
		return {
			typeSymbol: {
				'kein Filter': 'kein Filter',
				gleich: '=',
				kleiner: '<',
				größer: '>',
				zwischen: 'zwischen',
			},
			comparisonRestriction: {
				type: 'kein Filter',
				typeSymbol: 'kein Filter',
				unit: this.profile.valueDefinition.allowedUnits[0].display,
				value: '0',
				min: '0',
				max: '0',
			},
			isFilterOptional: this.profile.valueDefinition?.optional,
		}
	},

	watch: {
		'comparisonRestriction.type'() {
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
					this.$emit('get-selected-option', {
						type: 'quantityType',
						value: {},
						isFilterOptional: this.isFilterOptional,
						completeFilter: false,
					})
				} else if (this.comparisonRestriction.type === 'zwischen') {
					this.$emit('get-selected-option', {
						type: 'quantityType',
						value: this.comparisonRestriction,
						isFilterOptional: this.isFilterOptional,
						completeFilter: this.comparisonRestriction.type && (this.comparisonRestriction.min < this.comparisonRestriction.max),
					})

				} else {
					this.$emit('get-selected-option', {
						type: 'quantityType',
						value: this.comparisonRestriction,
						isFilterOptional: this.isFilterOptional,
						completeFilter: this.comparisonRestriction.type.length > 0 && this.comparisonRestriction.value.length > 0,
					 })
				}

			},
			deep: true,
		},

		isResetDisabled() {
			if (this.isResetDisabled && this.comparisonRestriction.type !== 'kein Filter') {
				this.comparisonRestriction.type = 'kein Filter'
				this.comparisonRestriction.typeSymbol = 'kein Filter'
				this.comparisonRestriction.unit = this.profile.valueDefinition.allowedUnits[0].display
				this.comparisonRestriction.value = '0'
				this.comparisonRestriction.min = '0'
				this.comparisonRestriction.max = '0'
			}
		},

	},

	created() {
		this.$emit('get-selected-option', {
			type: 'quantityType',
			value: {},
			isFilterOptional: this.isFilterOptional,
			completeFilter: false,
		})
	},
	methods: {
		checkEmptyValue(event, tag) {
			if (event.target.value.length === 0) {
				this.comparisonRestriction[tag] = '0'
			}
		},
	},
}
</script>

<style scoped>
select, input {
	margin: 3px !important;
}

.flex-row {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
}

.font-bold {
	font-weight: 500;
}

.value-unit {
	column-gap: 20px !important;
}

.content-option__header {
	font-weight: 500;
}

.content-option__header span {
	color: red;
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

.content-option {
	display: flex;
	flex-direction: column;
	column-gap: 20px;
	margin: 10px 20px;
}

.dialog-card {
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 10px 20px;
	margin-bottom: 20px;
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
}
</style>
