<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->

	<div class="feasibility-query__display">
		<div class="display-header">
			<p>Ausgewählte Merkmale</p>
		</div>
		<div class="display-content">
			<div id="Einschlusskriterien" class="display-textfield">
				<div v-if="selectedCharacteristicsEin.length > 0">
					<draggable v-model="selectedCharacteristicsEinLocal" @end="$emit('update-div-sequence', 'einschlusskriterien', selectedCharacteristicsEinLocal)">
						<div v-for="(characteristic, index) in selectedCharacteristicsEinLocal" :key="index" style="position: relative">
							<!-- can be changed sequence -->
							<div class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'Einschlusskriterien')">
										{{ characteristic.display }}
									</div>
									<div v-if="characteristic.conceptType" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.conceptType.value" :key="value_index">
											{{ value }}
										</span>
									</div>
									<div v-if="characteristic.quantityType" class="selected-criteria-condition">
										<p v-if="characteristic.quantityType.value.type === 'zwischen'">
											{{ characteristic.quantityType.value.type }} {{ characteristic.quantityType.value.min }} und {{ characteristic.quantityType.value.max }} {{ characteristic.quantityType.value.unit }}
										</p>
										<p v-else>
											{{ characteristic.quantityType.value.typeSymbol }} {{ characteristic.quantityType.value.value }} {{ characteristic.quantityType.value.unit }}
										</p>
									</div>
									<div v-if="characteristic.timeRange?.value.fromDate" class="selected-criteria-condition">
										<p v-if="characteristic.timeRange.value.type === 'zwischen'">
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }} und {{ characteristic.timeRange.value.toDate }}
										</p>
										<p v-else>
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right">
									<button class="delete-btn" @click="deleteBtn(index, 'Einschlusskriterien')">
										<svg role="img"
											width="20px"
											height="20px"
											aria-hidden="true"
											focusable="false"
											data-prefix="fas"
											data-icon="times"
											class="svg-inline--fa fa-times fa-w-11 fa-lg"
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 352 512"><path fill="black" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" /></svg>
									</button>
								</div>
							</div>
							<button v-if="index !== (selectedCharacteristicsEin.length - 1) && (criteriaLogic.einschlussCriteria[index] === 'and')"
								class="combining-operator"
								@click="switchLogic('einschlussCriteria', index, 'and')">
								UND
								<svg role="img"
									width="12px"
									height="12px"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="circle"
									class="svg-inline--fa fa-circle fa-w-16"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z" />
								</svg>
							</button>
							<button v-if="criteriaLogic.einschlussCriteria[index] === 'or'"
								class="combining-operator or-operator"
								@click="switchLogic('einschlussCriteria', index, 'or')">
								<svg role="img"
									width="12px"
									height="12px"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="circle"
									class="svg-inline--fa fa-circle fa-w-16"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z" />
								</svg>
								ODER
							</button>
						</div>
					</draggable>
				</div>
			</div>
			<div class="pipe" />
			<div id="Ausschlusskriterien" class="display-textfield">
				<div v-if="selectedCharacteristicsAus.length > 0">
					<draggable v-model="selectedCharacteristicsAusLocal" @end="$emit('update-div-sequence', 'ausschlusskriterien', selectedCharacteristicsAusLocal)">
						<div v-for="(characteristic, index) in selectedCharacteristicsAusLocal" :key="index" style="position: relative">
							<div class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'Ausschlusskriterien')">
										{{ characteristic.display }}
									</div>
									<div v-if="characteristic.conceptType" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.conceptType.value" :key="value_index">
											{{ value }}
										</span>
									</div>
									<div v-if="characteristic.quantityType" class="selected-criteria-condition">
										<p v-if="characteristic.quantityType.value.type === 'zwischen'">
											{{ characteristic.quantityType.value.type }} {{ characteristic.quantityType.value.min }} und {{ characteristic.quantityType.value.max }} {{ characteristic.quantityType.value.unit }}
										</p>
										<p v-else>
											{{ characteristic.quantityType.value.typeSymbol }} {{ characteristic.quantityType.value.value }} {{ characteristic.quantityType.value.unit }}
										</p>
									</div>
									<div v-if="characteristic.timeRange?.value.fromDate" class="selected-criteria-condition">
										<p v-if="characteristic.timeRange.value.type === 'zwischen'">
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }} und {{ characteristic.timeRange.value.toDate }}
										</p>
										<p v-else>
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right">
									<button class="delete-btn" @click="deleteBtn(index, 'Ausschlusskriterien')">
										<svg role="img"
											width="18px"
											height="18px"
											aria-hidden="true"
											focusable="false"
											data-prefix="fas"
											data-icon="times"
											class="svg-inline--fa fa-times fa-w-11 fa-lg"
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 352 512">
											<path fill="black" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z" />
										</svg>
									</button>
								</div>
							</div>
							<button v-if="index !== (selectedCharacteristicsAus.length - 1) && (criteriaLogic.ausschlussCriteria[index] === 'and')"
								class="combining-operator"
								@click="switchLogic('ausschlussCriteria', index, 'and')">
								UND
								<svg role="img"
									width="12px"
									height="12px"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="circle"
									class="svg-inline--fa fa-circle fa-w-16"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z" />
								</svg>
							</button>
							<button v-if="criteriaLogic.ausschlussCriteria[index] === 'or'"
								class="combining-operator or-operator"
								@click="switchLogic('ausschlussCriteria', index, 'or')">
								<svg role="img"
									width="12px"
									height="12px"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="circle"
									class="svg-inline--fa fa-circle fa-w-16"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z" />
								</svg>
								ODER
							</button>
						</div>
					</draggable>
				</div>
			</div>
		</div>
		<!-- <span v-for="(ein, ein_index) in selectedCharacteristicsEin" :key="ein_index">
			{{ ein.display }} / {{ criteriaLogic.einschlussCriteria[ein_index] }} /
		</span>
		<br>
		<span v-for="(aus, aus_index) in selectedCharacteristicsAus" :key="aus_index">
			{{ aus.display }} / {{ criteriaLogic.ausschlussCriteria[aus_index] }} /
		</span> -->
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import type { FilterInfo } from '../types/LimitationsSelectedCriteriaCardData'
import draggable from 'vuedraggable'

interface FeasibilityQueryDisplayData {
    criteriaLogic: {
        einschlussCriteria: Array<string>;
        ausschlussCriteria: Array<string>;
    }
    einschlussCriteriaOld: Array<string>;
    ausschlussCriteriaOld: Array<string>;
	selectedCharacteristicsEinLocal: Array<FilterInfo>;
	selectedCharacteristicsAusLocal: Array<FilterInfo>;
}

export default Vue.extend({
	name: 'FeasibilityQueryDisplay',
	components: {
		draggable,
	},

	props: {
		selectedCharacteristicsEin: {
			type: Array<FilterInfo>,
			required: true,
		},

		selectedCharacteristicsAus: {
			type: Array<FilterInfo>,
			required: true,
		},

		editCriteriaLimitation: {
			type: Function,
			default: () => {},
		},

		deleteCharacteristic: {
			type: Function,
			default: () => {},
		},
	},

	data(): FeasibilityQueryDisplayData {
		return {
			selectedCharacteristicsEinLocal: [...this.selectedCharacteristicsEin],
			selectedCharacteristicsAusLocal: [...this.selectedCharacteristicsAus],
			criteriaLogic: {
				einschlussCriteria: [],
				ausschlussCriteria: [],
			},
			einschlussCriteriaOld: [],
			ausschlussCriteriaOld: [],
		}
	},

	watch: {
		selectedCharacteristicsEin(newVal) {
			this.selectedCharacteristicsEinLocal = [...newVal]

			// first input
			if (this.einschlussCriteriaOld.length === 0) {
				const length = newVal.length - this.einschlussCriteriaOld.length
				for (let i = 1; i < length; i++) {
					this.criteriaLogic.einschlussCriteria.push('and')
				}
			} else if (newVal.length > 0) {
				const length = newVal.length - this.einschlussCriteriaOld.length
				for (let i = 1; i <= length; i++) {
					this.criteriaLogic.einschlussCriteria.push('and')
				}
			}
			this.einschlussCriteriaOld = [...newVal]
		},

		selectedCharacteristicsAus(newVal) {
			this.selectedCharacteristicsAusLocal = [...newVal]

			// first input
			if (this.ausschlussCriteriaOld.length === 0) {
				const length = newVal.length - this.ausschlussCriteriaOld.length
				for (let i = 1; i < length; i++) {
					this.criteriaLogic.ausschlussCriteria.push('and')
				}
			} else if (this.selectedCharacteristicsAus.length > 0) {
				const length = newVal.length - this.ausschlussCriteriaOld.length
				for (let i = 1; i <= length; i++) {
					this.criteriaLogic.ausschlussCriteria.push('and')
				}
			}
			this.ausschlussCriteriaOld = [...newVal]
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {

	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		switchLogic(criteria: string, index: number, logic: string): void {
			if (criteria === 'einschlussCriteria') {
				if (logic === 'and') {
					this.criteriaLogic.einschlussCriteria.splice(index, 1, 'or')
				} else if (logic === 'or') {
					this.criteriaLogic.einschlussCriteria.splice(index, 1, 'and')
				}
			} else if (criteria === 'ausschlussCriteria') {
				if (logic === 'and') {
					this.criteriaLogic.ausschlussCriteria.splice(index, 1, 'or')
				} else if (logic === 'or') {
					this.criteriaLogic.ausschlussCriteria.splice(index, 1, 'and')
				}
			}
		},

		editLimitation(characteristic: FilterInfo, index: number, criteriaType: string) {
			this.$emit('edit-criteria-limitation', characteristic, index, criteriaType)
		},

		deleteBtn(index: number, criteriaType: string) {
			this.$emit('delete-characteristic', index, criteriaType)
			if (criteriaType === 'Einschlusskriterien') {
				if (this.criteriaLogic.einschlussCriteria[index - 1] === 'or') {
					this.criteriaLogic.einschlussCriteria.splice(index - 1, 1)
				} else this.criteriaLogic.einschlussCriteria.splice(index, 1)
			} else if (criteriaType === 'Ausschlusskriterien') {
				if (this.criteriaLogic.ausschlussCriteria[index - 1] === 'or') {
					this.criteriaLogic.ausschlussCriteria.splice(index - 1, 1)
				} else this.criteriaLogic.ausschlussCriteria.splice(index, 1)
			}
		},
	},
})
</script>
<style scoped>
.pipe--criteria {
	border-radius: 4px;
}

.feasibility-query__display {
	border: 1px solid #9ea9b3;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	box-shadow: none!important;
	margin-top: 20px;
	width: 100%;
}

.display-header {
	display: flex;
	align-items: center;
	padding-left: 10px;
	background: #5270a7;
	color: #ffffff;
	border-top-right-radius: 4px;
	border-top-left-radius: 4px;
	min-height: 3.5em;
}

.display-header p {
	font-size: 16px;
	font-weight: 500;
}

.display-content {
	display: flex;
	flex-direction: row;
	min-height: 150px;
}

.display-textfield {
	flex: 1 1 100%;
	border: dashed 1px black;
	margin: 20px;
	padding: 20px;
}

.selected-criteria-container {
	display: flex;
	border: 1px solid #5270a7;
	justify-content: flex-end;
	/* margin-bottom: 15px; */
}

.selected-criteria-container:last-child {
	margin-bottom: 0px;
}

/* .selected-criteria-container:hover {
    opacity: 0.2;
    cursor: pointer;
} */

.selected-criteria-left {
	width: 15%;
	padding: 10px 0px;
	background-color: #5270a7;
}

.selected-criteria-middle {
	display: flex;
	flex-direction: column;
	width: 80%;
	padding: 10px;
}

.selected-criteria-right {
	display: flex;
	justify-content: center;
	width: 7%;
	background-color: #5270a7;
	padding: 10px 0px;
}

.selected-criteria-display {
	font-weight: 700;
	margin-bottom: 10px;
}

.selected-criteria-display:hover {
	text-decoration: underline;
	cursor: pointer;
}

.selected-criteria-condition {
	padding-left: 5px;
	margin-bottom: 5px;
	background-color: #adbcd7;
}

.selected-criteria-condition:last-child {
	margin-bottom: 0px;
}

.selected-criteria-condition span {
	float: left;
}

.selected-criteria-condition span::before {
	content: ',';
}

.selected-criteria-condition span:first-child::before {
	content: none;
}

.combining-operator {
	margin-left: 10px;
	padding: 5px 10px;
	border: solid 1px black;
	font-size: small;
	font-weight: 600;
	min-height: 20px;
}

.or-operator {
	position: absolute;
	margin-top: -15px;
	z-index: 1;
}

.delete-btn {
	display: flex;
	border: none;
	outline: none;
	height: 0px;
	margin: 0px;
	padding: 0px;
	background-color: unset;
}

.selected-criteria-right button.delete-btn:active {
	background-color: unset;
}

.delete-btn img {
	width: 25px;
	height: 20px;
}
</style>
