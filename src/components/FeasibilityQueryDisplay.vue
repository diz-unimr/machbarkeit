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
			<div id="inclusionCriteria" class="display-textfield">
				<div v-if="draggableInclusionCharacteristics?.length > 0">
					<draggable v-model="draggableInclusionCharacteristics">
						<div v-for="(characteristic, index) in draggableInclusionCharacteristics"
							:key="index"
							style="position: relative">
							<!-- can be changed sequence -->
							<div class="selected-criteria-container" :style="{ 'border': '1px solid' + draggableInclusionCharacteristics[index].color }">
								<div class="selected-criteria-left" :style="{ 'background-color': draggableInclusionCharacteristics[index].color}" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'inclusionCriteria')">
										{{ characteristic.display }} ({{ characteristic.termCodes[0].code }})
									</div>
									<!-- Concept Type -->
									<div v-if="Array.isArray(characteristic.valueFilter?.selectedConcepts) && characteristic.valueFilter.selectedConcepts.length > 0" class="selected-criteria-condition">
										<span v-for="(value, value_index) in characteristic.valueFilter.selectedConcepts" :key="value_index">
											{{ value.display }}
										</span>
									</div>
									<!-- Quantity Type -->
									<div v-if="characteristic.valueFilter && typeof characteristic.valueFilter === 'object' && Object.keys(characteristic.valueFilter).length > 0" class="selected-criteria-condition">
										<p v-if="characteristic.valueFilter.type === 'quantity-range'">
											Zwischen {{ characteristic.valueFilter.minValue }} - {{ characteristic.valueFilter.maxValue }} {{ characteristic.valueFilter.unit.display }}
										</p>
										<p v-if="characteristic.valueFilter?.type === 'quantity-comparator'">
											{{ characteristic.valueFilter?.comparator === 'lt' ? '<' : characteristic.valueFilter?.comparator === 'gt' ? '>' : '=' }} {{ characteristic.valueFilter?.value }} {{ characteristic.valueFilter?.unit.display }}
										</p>
									</div>
									<!-- TimeRange Time -->
									<div v-if="characteristic.timeRestriction && Object.keys(characteristic.timeRestriction).length > 0" class="selected-criteria-condition">
										<p v-if="checkDateType(characteristic.timeRestriction) === 'between'">
											Von {{ characteristic.timeRestriction.afterDate }} bis {{ characteristic.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="checkDateType(characteristic.timeRestriction) === 'at'">
											Am {{ characteristic.timeRestriction.afterDate }}
										</p>
										<p v-else-if="checkDateType(characteristic.timeRestriction) === 'before'">
											Vor {{ characteristic.timeRestriction.beforeDate }}
										</p>
										<p v-else-if="checkDateType(characteristic.timeRestriction) === 'after'">
											Nach {{ characteristic.timeRestriction.afterDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right" :style="{ 'background-color': draggableInclusionCharacteristics[index].color}">
									<button class="delete-btn" @click="deleteCharacteristic(index, 'inclusionCriteria')">
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
							<button v-if="index !== (draggableInclusionCharacteristics.length - 1) && (inclusionLogic[index] === 'and')"
								class="combining-operator"
								@click="switchLogic('inclusionCriteria', index, 'and')">
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
							<button v-if="index !== (draggableInclusionCharacteristics.length - 1) && inclusionLogic[index] === 'or'"
								class="combining-operator or-operator"
								@click="switchLogic('inclusionCriteria', index, 'or')">
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
			<!-- <div class="pipe" />
			<div id="exclusionCriteria" class="display-textfield">
				<div v-if="draggableExclusionCharacteristics.characteristics.length > 0">
					<draggable v-model="draggableExclusionCharacteristics.characteristics" @end="$emit('update-characteristics', 'exclusionCriteria', draggableExclusionCharacteristics)">
						<div v-for="(characteristic, index) in draggableExclusionCharacteristics.characteristics" :key="index" style="position: relative">
							<div class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display" @click="editLimitation(characteristic, index, 'exclusionCriteria')">
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
									<button class="delete-btn" @click="deleteCharacteristic(index, 'ausschlusskriterien')">
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
							<button v-if="index !== (draggableExclusionCharacteristics.characteristics.length - 1) && (draggableExclusionCharacteristics.logic[index] === 'and')"
								class="combining-operator"
								@click="switchLogic('ausschlusskriterien', index, 'and')">
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
							<button v-if="draggableExclusionCharacteristics.logic[index] === 'or'"
								class="combining-operator or-operator"
								@click="switchLogic('ausschlusskriterien', index, 'or')">
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
			</div> -->
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import draggable from 'vuedraggable'
import type { Criterion } from '../types/OntologyPanelData'
import type { TimeRangeType } from '../types/TimeRangeOptionsData'
import type { CharacteristicGroup } from '../types/FeasibilityQueryContainerData'

export default Vue.extend({
	name: 'FeasibilityQueryDisplay',
	components: {
		draggable,
	},

	computed: {
		selectedCharacteristics() {
			return this.$store.state.selectedCharacteristics
		},

		draggableInclusionCharacteristics: {
			get() {
				return this.selectedCharacteristics.inclusionCriteria.characteristics
			},
			set(value: CharacteristicGroup['characteristics']) {
				this.$store.dispatch('updateCharacteristicOrder', { section: 'inclusionCriteria', value })
			},
		},

		inclusionLogic: {
			get() {
				return this.selectedCharacteristics.inclusionCriteria.logic
			},
		},

		hasCharacteristics(): boolean {
			return this.draggableInclusionCharacteristics.length > 0 || this.draggableExclusionCharacteristics.length > 0
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
		checkDateType(timeRangeData: TimeRangeType['timeRestriction']): string | null {
			const afterDate = timeRangeData.afterDate && timeRangeData.afterDate.length > 0 ? new Date(timeRangeData.afterDate as string).getTime() : null
			const beforeDate = timeRangeData.beforeDate && timeRangeData.beforeDate.length > 0 ? new Date(timeRangeData.beforeDate as string).getTime() : null

			if (afterDate !== null && beforeDate !== null) {
				if (afterDate === beforeDate) {
					return 'at'
				} else if (afterDate < beforeDate) {
					return 'between'
				}
			}

			if (afterDate !== null) return 'after'
			if (beforeDate !== null) return 'before'
			return null
		},

		switchLogic(criteriaType: string, index: number, logic: string): void {
			if (logic === 'and') {
				this.$store.dispatch('switchLogic', { index, section: criteriaType, logic: 'or' })
			} else if (logic === 'or') {
				this.$store.dispatch('switchLogic', { index, section: criteriaType, logic: 'and' })
			}
		},

		editLimitation(characteristic: Criterion, index: number, criteriaType: string) {
			this.$emit('edit-filter', characteristic.id, index, criteriaType)
		},

		deleteCharacteristic(index: number, criteriaType: string) {
			this.$store.dispatch('removeCharacteristics', { index, section: criteriaType })
			if (index > 0 && this.inclusionLogic[index - 1] === 'or') {
				this.$store.dispatch('removeLogic', { index: index - 1, section: criteriaType })
			} else this.$store.dispatch('removeLogic', { index, section: criteriaType })
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
	justify-content: space-between;
	border: 1px solid #5270a7;
}

.selected-criteria-container:last-child {
	margin-bottom: 0px;
}

.selected-criteria-left {
	width: clamp(40px, 12%, 70px);
	padding: 10px 0px;
	cursor: all-scroll;
	background-color: #5270a7;
}

.selected-criteria-middle {
	display: flex;
	flex: 1;
	flex-direction: column;
	padding: 8px;
	padding-left: clamp(10px, 5%, 25px);
}

.selected-criteria-right {
	display: flex;
	justify-content: center;
	width: clamp(10px, 7%, 50px);
	background-color: #5270a7;
	padding: 10px 0px;
}

.selected-criteria-right button{
	margin: 3px 3px 3px 3px !important;
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
	min-width: 70px;
	margin-left: 10px !important;
	padding: 5px 10px;
	border: solid 1px black;
	border-radius: 14px !important;
	font-size: small !important;
	font-weight: 600;
}

button.combining-operator:active {
	background-color: var(--color-primary-element-light) !important;

}

.or-operator {
	position: absolute !important;
	margin-top: -17px !important;
	z-index: 1 !important;
}

.delete-btn {
	display: flex;
	align-items: center;
	border: none;
	outline: none;
	min-height: 20px;
	margin: 0px;
	padding: 0px;
	background-color: unset !important;
}

.selected-criteria-right button.delete-btn:active {
	background-color: unset !important;
}

.selected-criteria-right button:hover {
	background-color: #5270a7;
}

.delete-btn img {
	width: 25px;
	height: 20px;
}
</style>
