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
					<draggable v-model="draggableInclusionCharacteristics" @end="$emit('update-characteristics', 'einschlusskriterien', draggableInclusionCharacteristics, queryData)">
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
									<div v-if="characteristic.selectedFilter && isConceptType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.selectedFilter.valueFilter?.selectedConcepts" :key="value_index">
											{{ value.display }}
										</span>
									</div>
									<div v-if="characteristic.selectedFilter && isQuantityType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<p v-if="characteristic.selectedFilter.valueFilter?.type === 'quantity-range'">
											Zwischen {{ characteristic.selectedFilter.valueFilter?.minValue }} - {{ characteristic.selectedFilter.valueFilter?.maxValue }} {{ characteristic.selectedFilter.valueFilter?.unit.display }}
										</p>
										<p v-if="characteristic.selectedFilter.valueFilter?.type === 'quantity-comparator'">
											{{ characteristic.selectedFilter.valueFilter?.comparator === 'lt' ? '<' : characteristic.selectedFilter.valueFilter?.comparator === 'gt' ? '>' : '=' }} {{ characteristic.selectedFilter.valueFilter?.value }} {{ characteristic.selectedFilter.valueFilter?.unit.display }}
										</p>
									</div>
									<div v-if="characteristic.selectedFilter && isTimeRangeType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<p v-if="characteristic.selectedFilter?.timeRestriction?.afterDate && characteristic.selectedFilter?.timeRestriction.beforeDate && new Date(characteristic.selectedFilter?.timeRestriction?.afterDate) < new Date(characteristic.selectedFilter?.timeRestriction.beforeDate)">
											Von {{ characteristic.selectedFilter?.timeRestriction.afterDate }} bis {{ characteristic.selectedFilter?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedFilter?.timeRestriction?.afterDate === characteristic.selectedFilter?.timeRestriction?.beforeDate">
											Am {{ characteristic.selectedFilter?.timeRestriction?.afterDate }}
										</p>
										<p v-else-if="!characteristic.selectedFilter?.timeRestriction?.afterDate && characteristic.selectedFilter?.timeRestriction?.beforeDate">
											Vor {{ characteristic.selectedFilter?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedFilter?.timeRestriction?.afterDate && !characteristic.selectedFilter?.timeRestriction.beforeDate">
											Nach {{ characteristic.selectedFilter?.timeRestriction.afterDate }}
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
									<div v-if="characteristic.selectedFilter && isConceptType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.selectedFilter.valueFilter?.selectedConcepts" :key="value_index">
											{{ value.display }}
										</span>
									</div>
									<div v-if="characteristic.selectedFilter && isQuantityType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<p v-if="characteristic.selectedFilter.valueFilter?.type === 'quantity-range'">
											Zwischen {{ characteristic.selectedFilter.valueFilter?.minValue }} - {{ characteristic.selectedFilter.valueFilter?.maxValue }} {{ characteristic.selectedFilter.valueFilter?.unit.display }}
										</p>
										<p v-if="characteristic.selectedFilter.valueFilter?.type === 'quantity-comparator'">
											{{ characteristic.selectedFilter.valueFilter?.comparator === 'lt' ? '<' : characteristic.selectedFilter.valueFilter?.comparator === 'gt' ? '>' : '=' }} {{ characteristic.selectedFilter.valueFilter?.value }} {{ characteristic.selectedFilter.valueFilter?.unit.display }}
										</p>
									</div>
									<div v-if="characteristic.selectedFilter && isTimeRangeType(characteristic.selectedFilter)" class="selected-criteria-condition">
										<p v-if="characteristic.selectedFilter?.timeRestriction?.afterDate && characteristic.selectedFilter?.timeRestriction.beforeDate && new Date(characteristic.selectedFilter?.timeRestriction?.afterDate) < new Date(characteristic.selectedFilter?.timeRestriction.beforeDate)">
											Von {{ characteristic.selectedFilter?.timeRestriction.afterDate }} bis {{ characteristic.selectedFilter?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedFilter?.timeRestriction?.afterDate === characteristic.selectedFilter?.timeRestriction?.beforeDate">
											Am {{ characteristic.selectedFilter?.timeRestriction?.afterDate }}
										</p>
										<p v-else-if="!characteristic.selectedFilter?.timeRestriction?.afterDate && characteristic.selectedFilter?.timeRestriction?.beforeDate">
											Vor {{ characteristic.selectedFilter?.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="characteristic.selectedFilter?.timeRestriction?.afterDate && !characteristic.selectedFilter?.timeRestriction.beforeDate">
											Nach {{ characteristic.selectedFilter?.timeRestriction.afterDate }}
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
		<!-- <div>
			<span v-for="(ein, ein_index) in selectedInclusionCharacteristics" :key="ein_index">
				{{ ein.display }} / {{ characteristicsLogic.inclusionCriteria[ein_index] }} /
			</span>
		</div>
		<br>
		<div>
			<span v-for="(aus, aus_index) in selectedExclusionCharacteristics" :key="aus_index">
				{{ aus.display }} / {{ characteristicsLogic.exclusionCriteria[aus_index] }} /
			</span>
		</div> -->
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import draggable from 'vuedraggable'
import type { Criterion } from '../types/OntologySearchTreeModalData.ts'
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRangeType } from '../types/TimeRangeOptionsData.ts'

type QueryCriterionData = {
			termCodes: Criterion['termCodes'];
			context: Criterion['context'];
		} & (ConceptType | QuantityType | TimeRangeType | undefined)

export interface FeasibilityQueryDisplayData {
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
		inclusionCriteria?: Array<QueryCriterionData>[];
		exclusionCriteria?: Array<QueryCriterionData>[];
	};
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
				version: '',
				display: '',
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
			this.updateQueryData()
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
		isConceptType(filter: ConceptType | QuantityType | TimeRangeType): filter is ConceptType {
			return 'valueFilter' in filter && filter.valueFilter!.type === 'concept'
		},

		isQuantityType(filter: ConceptType | QuantityType | TimeRangeType): filter is QuantityType {
			return 'valueFilter' in filter && (filter.valueFilter!.type === 'quantity-range' || filter.valueFilter!.type === 'quantity-comparator')
		},

		isTimeRangeType(filter: ConceptType | QuantityType | TimeRangeType): filter is TimeRangeType {
			return 'timeRestriction' in filter
		},

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
			this.queryData.inclusionCriteria = []
			this.queryData.exclusionCriteria = []
			if (this.selectedInclusionCharacteristics.length > 0) {
				let tempIndex = 0
				for (let i = 0; i < this.selectedInclusionCharacteristics.length; i++) {
					const selectedCharacteristic = {
						termCodes: this.selectedInclusionCharacteristics[i].termCodes,
						context: this.selectedInclusionCharacteristics[i].context,
						...(this.selectedInclusionCharacteristics[i].selectedFilter || {})
					} as QueryCriterionData ;

					if (i === 0) {
						this.queryData.inclusionCriteria.push([selectedCharacteristic])
					} else {
						if (this.characteristicsLogic.inclusionCriteria[i-1] === 'or') {
							this.queryData.inclusionCriteria[tempIndex].push(selectedCharacteristic)
						} else if (this.characteristicsLogic.inclusionCriteria[i-1] === 'and') {
							this.queryData.inclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						}
					}
					
				}
			}
			
			if (this.selectedExclusionCharacteristics.length > 0) {
				let tempIndex = 0

				for (let i = 0; i < this.selectedExclusionCharacteristics.length; i++) {
					const selectedCharacteristic = {
						termCodes: this.selectedExclusionCharacteristics[i].termCodes,
						context: this.selectedExclusionCharacteristics[i].context,
						...(this.selectedExclusionCharacteristics[i].selectedFilter || {})
					} as QueryCriterionData ;
					if (i === 0) {
						this.queryData.exclusionCriteria.push([selectedCharacteristic])
					} else {
						if (this.characteristicsLogic.exclusionCriteria[i-1] === 'or') {
							this.queryData.exclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						} else if (this.characteristicsLogic.exclusionCriteria[i-1] === 'and') {
							this.queryData.exclusionCriteria[tempIndex].push(selectedCharacteristic)
						}
					}
					
				}
			}
			this.$emit('get-update-query-data', this.queryData)
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
