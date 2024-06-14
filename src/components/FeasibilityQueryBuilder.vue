<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="query-builder-container">
		<div class="feasibility-query__input">
			<div class="criteria-wrapper">
				<div class="criteria">
					<div class="criteria-title">
						Einschlusskriterien
					</div>
					<div class="criteria-search-input">
						<div class="search-button">
							<button id="einschlusskriterien" @click="toggleSearchCriteria('Einschlusskriterien')">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="folder"
									class="svg-inline--fa fa-folder fa-w-16 fa-2x"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
								</svg>
							</button>
						</div>
						<div class="search-input">
							<NcTextField :value.sync="einschlussTextSerach"
								style="font-size: 16px;"
								label="Code oder Suchbegriff eingeben"
								trailing-button-icon="close"
								placeholder=" "
								:show-trailing-button="einschlussTextSerach !== ''"
								@trailing-button-click="einschlussTextSerach = ''"
								@input="searchCodeEinschlusskriterien">
								<Magnify :size="20" />
							</NcTextField>
						</div>
					</div>
				</div>
				<div class="pipe pipe--criteria" />
				<div class="criteria">
					<div class="criteria-title">
						Ausschlusskriterien
					</div>
					<div class="criteria-search-input">
						<div class="search-button">
							<button id="ausschlusskriterien" @click="toggleSearchCriteria('Ausschlusskriterien')">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="folder"
									class="svg-inline--fa fa-folder fa-w-16 fa-2x"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
								</svg>
							</button>
						</div>
						<div class="search-input">
							<NcTextField :value.sync="ausschlussTextSerach"
								style="font-size: 16px;"
								label="Code oder Suchbegriff eingeben"
								trailing-button-icon="close"
								placeholder=" "
								:show-trailing-button="ausschlussTextSerach !== ''"
								@trailing-button-click="ausschlussTextSerach = ''"
								@input="searchCodeAusschlusskriterien">
								<Magnify :size="20" />
							</NcTextField>
						</div>
					</div>
				</div>
			</div>
			<SearchTreeOverlayContent v-if="isCriteriaOverlayOpen"
				:criteria-type="criteriaOverlayType"
				@get-selected-criteria="getSelectedCriteria"
				@toggle-search-criteria="toggleSearchCriteria" />
		</div>

		<LimitationsSelectedFeatures v-if="isCriteriaOptionOpen"
			:selected-criteria="selectedCriteria"
			:ui-profile="uiProfile"
			@dialog-close="selectionOntologyDiaglogClose"
			@get-filter-info="getFilterInfo"
			@update-selected-criteria="updateSelectedCriteria"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<div class="feasibility-query__output">
			<div class="output-header">
				<p>Ausgewählte Merkmale</p>
			</div>
			<div class="output-content">
				<div id="Einschlusskriterien" class="output-textfield">
					<div v-if="selectedCharacteristicsEin.length > 0">
						<div>
							<div v-for="(characteristic, index) in selectedCharacteristicsEin" :key="index" class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display">
										{{ characteristic.display }}
									</div>
									<div v-if="characteristic.conceptType" class="selected-criteria-condition">
										<span v-for="(type, type_index) in characteristic.conceptType.value" :key="type_index">
											{{ type }}
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
									<div v-if="characteristic.timeRange" class="selected-criteria-condition">
										<p v-if="characteristic.timeRange.value.type === 'zwischen'">
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }} und {{ characteristic.timeRange.value.toDate }}
										</p>
										<p v-else>
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right">
									<!-- @click="deleteCard($vnode.data.attrs.id)" -->
									<button class="delete-btn" @click="deleteCharacteristic(index, 'Einschlusskriterien')">
										<!-- <img :src="imgDelete"> -->

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
						</div>
					</div>

					<!-- <template v-if="selectedOntologyArr.length > 0">
						<ul v-for="(selectedElement, index) in selectedOntologyArr" :key="index">
							<div>
								{{ selectedElement.display }}
								<button @click="deleteItem(selectedElement.id)">
									Delete
								</button>
							</div>
						</ul>
					</template> -->
				</div>
				<div class="pipe" />
				<div id="Ausschlusskriterien" class="output-textfield">
					<div v-if="selectedCharacteristicsAus.length > 0">
						<div>
							<div v-for="(characteristic, index) in selectedCharacteristicsAus" :key="index" class="selected-criteria-container">
								<div class="selected-criteria-left" />
								<div class="selected-criteria-middle">
									<div class="selected-criteria-display">
										{{ characteristic.display }}
									</div>
									<div v-if="characteristic.conceptType" class="selected-criteria-condition">
										<span v-for="(type, type_index) in characteristic.conceptType.value" :key="type_index">
											{{ type }}
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
									<div v-if="characteristic.timeRange" class="selected-criteria-condition">
										<p v-if="characteristic.timeRange.value.type === 'zwischen'">
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }} und {{ characteristic.timeRange.value.toDate }}
										</p>
										<p v-else>
											{{ characteristic.timeRange.value.type }} {{ characteristic.timeRange.value.fromDate }}
										</p>
									</div>
								</div>
								<div class="selected-criteria-right">
									<!-- @click="deleteCard($vnode.data.attrs.id)" -->
									<button class="delete-btn" @click="deleteCharacteristic(index, 'Ausschlusskriterien')">
										<!-- <img :src="imgDelete"> -->

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
						</div>
					</div>

					<!-- <template v-if="selectedOntologyArr.length > 0">
						<ul v-for="(selectedElement, index) in selectedOntologyArr" :key="index">
							<div>
								{{ selectedElement.display }}
								<button @click="deleteItem(selectedElement.id)">
									Delete
								</button>
							</div>
						</ul>
					</template> -->
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import SearchTreeOverlayContent from './SearchTreeOverlayContent.vue'
import LimitationsSelectedFeatures from './Limitations/LimitationsSelectedFeatures.vue'
import type { FilterInfo, FeasibilityQueryBuilderData } from '../types/FeasibilityQueryBuilderData'
import type { CriteriaResponse } from '../types/SearchTreeOverlayContentData.ts'
// import type { updatedOntologyData } from './Limitations/LimitationsSelectedFeatures.vue'

interface SelectedCriteriaData {
	criteriaType: string;
	selectedItems: CriteriaResponse[];
}

interface updatedCriteriaData {
	id: string | number
	data: {
		type: string;
		item: object;
	};
}

export default Vue.extend({
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		SearchTreeOverlayContent,
		LimitationsSelectedFeatures,
	},

	data(): FeasibilityQueryBuilderData {
		return {
			uiProfile: null,
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isCriteriaOptionOpen: false,
			isCriteriaOverlayOpen: false,
			isEinschlusskriterienOverlayOpen: false,
			isAusschlusskriterienOverlayOpen: false,
			criteriaOverlayType: '',
			selectedCriteria: null,
			selectedCharacteristicsEin: [],
			selectedCharacteristicsAus: [],
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete-black.png',
		}
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getUiProfile()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getUiProfile() {
			const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ui_profile'))
			this.uiProfile = response.data
		},

		toggleSearchCriteria(type: string) {
			this.criteriaOverlayType = type
			this.isCriteriaOverlayOpen = !this.isCriteriaOverlayOpen
		},

		selectionOntologyDialogOpen() {
			this.isCriteriaOptionOpen = true
		},

		selectionOntologyDiaglogClose() {
			this.isCriteriaOptionOpen = false
		},

		searchCodeEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = true
			this.isAusschlusskriterienOverlayOpen = false
		},

		searchCodeAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = true
			this.isEinschlusskriterienOverlayOpen = false
		},

		getSelectedCriteria(items: SelectedCriteriaData) {
			this.toggleSearchCriteria(items.criteriaType)
			this.selectionOntologyDialogOpen()
			this.selectedCriteria = items.selectedItems
		},

		getFilterInfo(object: FilterInfo[]) {
			// check
			/* if (this.comparisonRestriction.value === '' || this.comparisonRestriction.min === '' || this.comparisonRestriction.max === '') {
				this.comparisonRestriction.value = 0
				this.comparisonRestriction.min = 0
				this.comparisonRestriction.max = 0
			} */
			if (this.criteriaOverlayType === 'Einschlusskriterien') {
				this.selectedCharacteristicsEin.push(...object)
			} else if (this.criteriaOverlayType === 'Ausschlusskriterien') {
				this.selectedCharacteristicsAus.push(...object)
			}

		},

		updateSelectedCriteria(data: updatedCriteriaData) {
			this.selectedCriteria![data.id][data.data.type] = data.data.item
		},

		deleteSelectedCriteria(index: number) {
			this.selectedCriteria?.splice(index, 1)
		},

		deleteCharacteristic(index: number, criteriaType: string) {
			if (criteriaType === 'Einschlusskriterien') {
				this.selectedCharacteristicsEin.splice(index, 1)
			} else if (criteriaType === 'Ausschlusskriterien') {
				this.selectedCharacteristicsAus.splice(index, 1)
			}
		},
	},
})
</script>
<style scoped>

.query-builder-container {
	display: flex;
	flex-direction: column;
	width: 100%;
	row-gap: 20px;
	font-size: 16px;
}

.feasibility-query__input {
	display: flex;
	flex-direction: column;
	width: 100%;
}

.criteria-wrapper {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	width: 100%;
}

.criteria {
	flex: 1 1 100%;
	position: relative;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
}

.criteria-title {
	min-width: min-content;
	background-color: #5270a7;
	color: #ffffff;
	text-align: center;
	font-size: 16px;
	font-weight: 500;
	padding: 2px 0;
}

.criteria-search-input {
	flex-direction: row;
	display: flex;
	justify-content: space-around;
	align-items: baseline;
	margin: 10px;
}

.search-button {
	place-content: stretch center;
	align-items: stretch;
	flex-direction: row;
	display: flex;
}

.search-button button {
	border-radius: 4px;
	border-width: 1px;
	border-style: solid;
	height: 44px;
	width: 44px;
	line-height: unset;
	color: white;
	background-color: #5e6c78;
}

.search-button button:hover {
	background-color: #9ea9b3;
}

.search-button button:active {
	background-color: #5e6c78 !important;
	color: white !important;
}

.search-input {
	flex: 1 1 100%;
	max-width: 85%;
	font-size: 14px;
}

.pipe {
	flex: 1 1 100%;
	max-width: 1%;
	background-color: #5270a7;
}

.pipe--criteria {
	border-radius: 4px;
}

.feasibility-query__output {
	border: 1px solid #9ea9b3;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	box-shadow: none!important;
	width: 100%;
}

.output-header {
	display: flex;
	align-items: center;
	padding-left: 10px;
	background: #5270a7;
	color: #ffffff;
	border-top-right-radius: 4px;
	border-top-left-radius: 4px;
	min-height: 3.5em;
}

.output-header p {
	font-size: 16px;
	font-weight: 500;
}

.output-content {
	display: flex;
	flex-direction: row;
	min-height: 150px;
}

.output-textfield {
	flex: 1 1 100%;
	border: dashed 1px black;
	margin: 20px;
	padding: 20px;
}

.selected-criteria-container {
	display: flex;
	border: 1px solid #5270a7;
	margin-bottom: 15px;
}

.selected-criteria-container:last-child {
	margin-bottom: 0px;
}

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
	width: 5%;
	background-color: #5270a7;
	padding: 10px 0px;
}

.selected-criteria-display {
	font-weight: 700;
}

.selected-criteria-condition {
	padding-left: 5px;
	background-color: #adbcd7;
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

/* .selected-criteria-right button:focus-visible {
	background-color: red;
	color: red;
}*/

.delete-btn img {
	width: 25px;
	height: 20px;
}
</style>
