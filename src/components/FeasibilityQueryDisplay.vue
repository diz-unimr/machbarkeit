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
			<div id="einschlusskriterien" class="display-textfield">
				<div v-if="selectedInclusionCharacteristics.length > 0">
					<draggable v-model="draggableInclusionCharacteristics" @end="$emit('update-display-sequence', 'einschlusskriterien', draggableInclusionCharacteristics)">
						<div v-for="(characteristic, index) in selectedInclusionCharacteristics"
							:key="index"
							style="position: relative">
							<!-- can be changed sequence -->
							<div class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'einschlusskriterien')">
										{{ characteristic.display }}
									</div>
									{{ (characteristic.selectedCriterion instanceof ConceptType) }}
									<div v-if="characteristic.selectedCriterion?.valueFilter?.selectedConcepts" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.selectedCriterion.valueFilter.selectedConcepts" :key="value_index">
											{{ value.display }}
										</span>
									</div>
									<div v-if="characteristic.selectedCriterion?.valueFilter?.type" class="selected-criteria-condition">
										<p v-if="characteristic.selectedCriterion.valueFilter?.type === 'quantity-range'">
											zwischen {{ characteristic.selectedCriterion.valueFilter?.minValue }} und {{ characteristic.selectedCriterion.valueFilter?.maxValue }} {{ characteristic.selectedCriterion.valueFilter?.unit.display }}
										</p>
										<p v-if="characteristic.selectedCriterion.valueFilter?.type === 'quantity-comparator'">
											{{ characteristic.selectedCriterion.valueFilter?.comparator === 'lt' ? '<' : characteristic.selectedCriterion.valueFilter?.comparator === 'gt' ? '>' : '=' }} {{ characteristic.selectedCriterion.valueFilter?.value }} {{ characteristic.selectedCriterion.valueFilter?.unit.display }}
										</p>
									</div>
									<div v-if="characteristic.selectedCriterion?.timeRestriction" class="selected-criteria-condition">
										<p v-if="new Date(characteristic.selectedCriterion?.timeRestriction.afterDate) < new Date(characteristic.selectedCriterion?.timeRestriction.beforeDate)">
											zwischen {{ characteristic.selectedCriterion?.timeRestriction.afterDate }} und {{ characteristic.selectedCriterion?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedCriterion?.timeRestriction.afterDate === characteristic.selectedCriterion?.timeRestriction.beforeDate">
											am {{ characteristic.selectedCriterion?.timeRestriction.afterDate }}
										</p>
										<p v-else-if="!characteristic.selectedCriterion?.timeRestriction.afterDate && characteristic.selectedCriterion?.timeRestriction.beforeDate">
											vor {{ characteristic.selectedCriterion?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedCriterion?.timeRestriction.afterDate && !characteristic.selectedCriterion?.timeRestriction.beforeDate">
											nach {{ characteristic.selectedCriterion?.timeRestriction.afterDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right">
									<button class="delete-btn" @click="deleteBtn(index, 'einschlusskriterien')">
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
							<button v-if="index !== (selectedInclusionCharacteristics.length - 1) && (characteristicsLogic.inclusionCriteria[index] === 'and')"
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
							<button v-if="characteristicsLogic.inclusionCriteria[index] === 'or'"
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
			<div id="ausschlusskriterien" class="display-textfield">
				<div v-if="selectedExclusionCharacteristics.length > 0">
					<draggable v-model="draggableExclusionCharacteristics" @end="$emit('update-display-sequence', 'ausschlusskriterien', draggableExclusionCharacteristics)">
						<div v-for="(characteristic, index) in selectedExclusionCharacteristics" :key="index" style="position: relative">
							<div class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'ausschlusskriterien')">
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
									<button class="delete-btn" @click="deleteBtn(index, 'ausschlusskriterien')">
										<svg role="img"
											width="20px"
											height="20px"
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
							<button v-if="index !== (selectedExclusionCharacteristics.length - 1) && (characteristicsLogic.exclusionCriteria[index] === 'and')"
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
							<button v-if="characteristicsLogic.exclusionCriteria[index] === 'or'"
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

		<div>
			<span v-for="(ein, ein_index) in selectedInclusionCharacteristics" :key="ein_index">
				{{ ein.display }} / {{ characteristicsLogic.inclusionCriteria[ein_index] }} /
			</span>
		</div>
		<br>
		<div>
			<span v-for="(aus, aus_index) in selectedExclusionCharacteristics" :key="aus_index">
				{{ aus.display }} / {{ characteristicsLogic.exclusionCriteria[aus_index] }} /
			</span>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData.ts'
import draggable from 'vuedraggable'
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRangeType } from '../types/TimeRangeOptionsData.ts'
import ConceptOptions from './Limitations/ConceptOptions.vue'

interface FeasibilityQueryDisplayData {
    characteristicsLogic: {
        inclusionCriteria: Array<string>;
        exclusionCriteria: Array<string>;
    }
    inclusionCriteriaOld: Array<string>;
	exclusionCriteriaOld: Array<string>;
	draggableInclusionCharacteristics: Array<Criterion>;
	draggableExclusionCharacteristics: Array<Criterion>;
	queryData: {
		version: string;
		display: string;
		inclusionCriteria: Array<ConceptType | QuantityType | TimeRangeType | null>[];
	};
	// typeSymbole: string;
	/* queryData: {
		version: string;
		display: string;
		inclusionCriteria: {
			termCodes: Array<Criterion['termCodes']>;
			context: Criterion['context'];
			timeRestriction: {
				beforDate: string | undefined;
				afterDate: string | undefined;
			} | undefined;
			valueFilter: {
				selectedConcepts: Array<Criterion['filterOptions']> | undefined;
				type: string | undefined;
				unit: Criterion['filterOptions'] | undefined;
				comparator: string | undefined;
				value: number | undefined;
				minValue: number | undefined;
				maxValue: number | undefined;
			} | undefined;
		}[] | undefined;
		exclusionCriteria: Array<Criterion['termCodes']>;
	} */
}

export default Vue.extend({
	name: 'FeasibilityQueryDisplay',
	components: {
		draggable,
	},

	props: {
		selectedInclusionCharacteristics: {
			type: Array<Criterion>,
			required: true,
		},

		selectedExclusionCharacteristics: {
			type: Array<Criterion>,
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
			draggableInclusionCharacteristics: [...this.selectedInclusionCharacteristics],
			draggableExclusionCharacteristics: [...this.selectedExclusionCharacteristics],
			characteristicsLogic: {
				inclusionCriteria: [],
				exclusionCriteria: [],
			},
			inclusionCriteriaOld: [],
			exclusionCriteriaOld: [],
			queryData: {
				version: '123',
				display: 'Test Query',
				inclusionCriteria: [[]],
			},
		}
	},

	watch: {
		selectedInclusionCharacteristics(newVal) {
			this.draggableInclusionCharacteristics = [...newVal]
			// first input
			if (this.inclusionCriteriaOld.length === 0) {
				for (let i = 1; i < newVal.length; i++) {
					this.characteristicsLogic.inclusionCriteria.push('and')
				}
			} else if (newVal.length > 0) {
				const length = newVal.length - this.inclusionCriteriaOld.length
				for (let i = 1; i <= length; i++) {
					this.characteristicsLogic.inclusionCriteria.push('and')
				}
			}
			this.inclusionCriteriaOld = [...newVal]
			this.updateQueryData()
		},

		selectedExclusionCharacteristics(newVal) {
			this.draggableExclusionCharacteristics = [...newVal]

			// first input
			if (this.exclusionCriteriaOld.length === 0) {
				const length = newVal.length - this.exclusionCriteriaOld.length
				for (let i = 1; i < length; i++) {
					this.characteristicsLogic.exclusionCriteria.push('and')
				}
			} else if (this.selectedExclusionCharacteristics.length > 0) {
				const length = newVal.length - this.exclusionCriteriaOld.length
				for (let i = 1; i <= length; i++) {
					this.characteristicsLogic.exclusionCriteria.push('and')
				}
			}
			this.exclusionCriteriaOld = [...newVal]
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
	updated() {
		//this.updateQueryData()
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		switchLogic(criteria: string, index: number, logic: string): void {
			if (criteria === 'einschlussCriteria') {
				if (logic === 'and') {
					this.characteristicsLogic.inclusionCriteria.splice(index, 1, 'or')
				} else if (logic === 'or') {
					this.characteristicsLogic.inclusionCriteria.splice(index, 1, 'and')
				}
			} else if (criteria === 'ausschlussCriteria') {
				if (logic === 'and') {
					this.characteristicsLogic.exclusionCriteria.splice(index, 1, 'or')
				} else if (logic === 'or') {
					this.characteristicsLogic.exclusionCriteria.splice(index, 1, 'and')
				}
			}
			this.updateQueryData()
		},

		editLimitation(characteristic: Criterion, index: number, criteriaType: string) {
			this.$emit('edit-criteria-limitation', characteristic, index, criteriaType)
		},

		deleteBtn(index: number, criteriaType: string) {
			this.$emit('delete-characteristic', index, criteriaType)
			if (criteriaType === 'einschlusskriterien') {
				if (this.characteristicsLogic.inclusionCriteria[index - 1] === 'or') {
					this.characteristicsLogic.inclusionCriteria.splice(index - 1, 1)
				} else this.characteristicsLogic.inclusionCriteria.splice(index, 1)
			} else if (criteriaType === 'ausschlusskriterien') {
				if (this.characteristicsLogic.exclusionCriteria[index - 1] === 'or') {
					this.characteristicsLogic.exclusionCriteria.splice(index - 1, 1)
				} else this.characteristicsLogic.exclusionCriteria.splice(index, 1)
			}
		},

		updateQueryData() {
			console.log('logic: ', this.characteristicsLogic)
			console.log(this.selectedInclusionCharacteristics)
			console.log(this.queryData)
			console.log(this.queryData.inclusionCriteria)
			console.log(this.queryData.inclusionCriteria[0])
			console.log(this.queryData.inclusionCriteria[3])
			this.queryData.inclusionCriteria = []
			// this.queryData.exclusionCriteria = []
			if (this.selectedInclusionCharacteristics) {
				let tempIndex = 0
				for (let i = 0; i < this.selectedInclusionCharacteristics.length; i++) {
					const selectedCharacteristic = this.selectedInclusionCharacteristics[i].selectedCriterion as ConceptType | QuantityType |TimeRangeType
					selectedCharacteristic.termCodes = selectedCharacteristic.termCodes.flat()
					console.log('selectedCharacteristic: ', selectedCharacteristic)
					// i === 0 && this.queryData.inclusionCriteria.splice(0, 0, [selectedCharacteristic])
					console.log(tempIndex)
					if (!this.queryData.inclusionCriteria[tempIndex]) {
						this.queryData.inclusionCriteria[tempIndex] = []
					}
					this.queryData.inclusionCriteria[tempIndex].push(...[selectedCharacteristic])
					if (this.characteristicsLogic.inclusionCriteria[i] === 'or') {
						console.log(this.selectedInclusionCharacteristics[i])
						this.queryData.inclusionCriteria[tempIndex].push(selectedCharacteristic)
					} else if (this.characteristicsLogic.inclusionCriteria[i] === 'and') {
						console.log(this.selectedInclusionCharacteristics[i])
						// this.queryData.inclusionCriteria.length > 0 && tempIndex++
						console.log(this.queryData.inclusionCriteria)
						this.queryData.inclusionCriteria.push([selectedCharacteristic])
						tempIndex++
						// this.queryData.inclusionCriteria[0].splice(tempIndex, 0, selectedCharacteristic)
					}
					i++
				}

				console.log(JSON.stringify(this.queryData))
				/* this.selectedInclusionCharacteristics.forEach(item => {
					const selectedCharacteristic = item.selectedCriterion as ConceptType | QuantityType |TimeRangeType
					selectedCharacteristic.termCodes = selectedCharacteristic.termCodes.flat()

					this.characteristicsLogic.inclusionCriteria.forEach(logic => {
						if (logic === 'or') {
							this.queryData.inclusionCriteria[index].push(selectedCharacteristic)
						}
					})
				}) */
			}
			/* if (this.selectedExclusionCharacteristics) {
				this.selectedExclusionCharacteristics.forEach(item => {
					const selectedCharacteristic = item.selectedCriterion as ConceptType | QuantityType |TimeRangeType
					selectedCharacteristic.termCodes = selectedCharacteristic.termCodes.flat()
					this.queryData.exclusionCriteria[0].push(selectedCharacteristic)
				})
			} */
			// this.$emit('get-update-query-data', this.queryData)
		},
	},
})
</script>

<style scoped>
.pipe {
	flex: 1 1 100%;
	max-width: 1%;
	background-color: #5270a7;
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
	min-height: 55px;
}

.display-header p {
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
}

.selected-criteria-container:last-child {
	margin-bottom: 0px;
}

.selected-criteria-left {
	width: 15%;
	padding: 10px 0px;
	cursor: all-scroll;
	background-color: #5270a7;
}

.selected-criteria-middle {
	display: flex;
	flex-direction: column;
	width: 80%;
	padding: 8px;
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
	margin-bottom: 8px;
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
	min-height: 20px;
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
