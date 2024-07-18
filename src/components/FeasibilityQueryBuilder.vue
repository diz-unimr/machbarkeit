<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-builder">
		<!-- <div class="feasibility-query__input"> -->
		<div class="feasibility-query-criteria-wrapper">
			<div class="criteria">
				<div class="criteria__title">
					Einschlusskriterien
				</div>
				<div class="criteria__search-input">
					<div class="criteria__search-input__button">
						<button id="einschlusskriterien" @click="toggleSearchCriteriaModal('Einschlusskriterien')">
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
					<div class="criteria__search-input__textbox">
						<NcTextField :value.sync="einschlussSearchInput"
							style="font-size: 16px;"
							label="Code oder Suchbegriff eingeben"
							trailing-button-icon="close"
							placeholder=" "
							:show-trailing-button="einschlussSearchInput !== ''"
							@trailing-button-click="closeSearchCriteriaModal"
							@focus="focusInput('Einschlusskriterien')">
							<Magnify :size="20" />
						</NcTextField>
					</div>
				</div>
			</div>
			<div class="pipe" />
			<div class="criteria">
				<div class="criteria__title">
					Ausschlusskriterien
				</div>
				<div class="criteria__search-input">
					<div class="criteria__search-input__button">
						<button id="ausschlusskriterien" @click="toggleSearchCriteriaModal('Ausschlusskriterien')">
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
					<div class="criteria__search-input__textbox">
						<NcTextField :value.sync="ausschlussSearchInput"
							style="font-size: 16px;"
							label="Code oder Suchbegriff eingeben"
							trailing-button-icon="close"
							placeholder=" "
							:show-trailing-button="ausschlussSearchInput !== ''"
							@trailing-button-click="closeSearchCriteriaModal"
							@focus="focusInput('Ausschlusskriterien')">
							<Magnify :size="20" />
						</NcTextField>
					</div>
				</div>
			</div>
		</div>

		<OntologySearchTreeModal v-if="isCriteriaContentOpen"
			:criteria-type="criteriaOverlayType"
			:einschluss-text-serach="einschlussTextSerach"
			:ausschluss-text-serach="ausschlussTextSerach"
			:is-ontology-button-clicked="isOntologyButtonClicked"
			@get-selected-criteria="getSelectedCriteria"
			@toggle-search-criteria-modal="toggleSearchCriteriaModal" />

		<LimitationsSelectedCriteriaModal v-if="isLimitationsCriteriaOpen"
			:selected-criteria="selectedCriteria"
			:ui-profile="uiProfile"
			:is-edit-filter-state="isEditFilterState"
			@get-selected-filter-info="getSelectedFilterInfo"
			@dialog-close="selectionOntologyDiaglogClose"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<FeasibilityQueryDisplay :selected-characteristics-ein="selectedCharacteristicsEin"
			:selected-characteristics-aus="selectedCharacteristicsAus"
			@update-div-sequence="updateDivSequence"
			@edit-criteria-limitation="editCriteriaLimitation"
			@delete-characteristic="deleteCharacteristic" />
		<!-- </div> -->
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import OntologySearchTreeModal from './OntologySearchTreeModal.vue'
import LimitationsSelectedCriteriaModal from './Limitations/LimitationsSelectedCriteriaModal.vue'
import FeasibilityQueryDisplay from './FeasibilityQueryDisplay.vue'
import type { FeasibilityQueryBuilderData } from '../types/FeasibilityQueryBuilderData'
import type { FilterInfo } from '../types/LimitationsSelectedCriteriaCardData.ts'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData.ts'
import debounce from 'lodash.debounce'

interface SelectedCriteriaData {
	criteriaType: string;
	selectedItems: OntologyTreeElement[];
}

export default Vue.extend({
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		OntologySearchTreeModal,
		LimitationsSelectedCriteriaModal,
		FeasibilityQueryDisplay,
	},

	data(): FeasibilityQueryBuilderData {
		return {
			uiProfile: null,
			einschlussSearchInput: '',
			ausschlussSearchInput: '',
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isLimitationsCriteriaOpen: false,
			isLimitationsCriteriaEditorOpen: false,
			isCriteriaContentOpen: false,
			isOntologyButtonClicked: false,
			criteriaOverlayType: '',
			selectedCriteria: null,
			selectedEditedCriteria: null,
			selectedEditedCriteriaIndex: null,
			selectedCharacteristicsEin: [],
			selectedCharacteristicsAus: [],
			isEditFilterState: false,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete-black.png',
		}
	},

	watch: {
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		einschlussSearchInput: debounce(function(this: any) {
			this.einschlussTextSerach = this.einschlussSearchInput
			if (this.einschlussTextSerach.length > 0) {
				this.criteriaOverlayType = 'Einschlusskriterien'
				this.isCriteriaContentOpen = true
			} else this.isCriteriaContentOpen = false
		}, 500),

		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		ausschlussSearchInput: debounce(function(this: any) {
			this.ausschlussTextSerach = this.ausschlussSearchInput
			if (this.ausschlussTextSerach.length > 0) {
				this.criteriaOverlayType = 'Ausschlusskriterien'
				this.isCriteriaContentOpen = true
			} else this.isCriteriaContentOpen = false
		}, 500),
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
		async getUiProfile(): Promise<void> {
			const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ui_profile'))
			this.uiProfile = response.data
		},

		toggleSearchCriteriaModal(type: string): void {
			this.isOntologyButtonClicked = !this.isOntologyButtonClicked
			this.criteriaOverlayType = type
			this.isCriteriaContentOpen = !this.isCriteriaContentOpen
			this.einschlussTextSerach = ''
			this.ausschlussTextSerach = ''
		},

		selectionOntologyDialogOpen(): void {
			this.isLimitationsCriteriaOpen = true
		},

		selectionOntologyDiaglogClose(): void {
			this.isLimitationsCriteriaOpen = false
			this.isLimitationsCriteriaEditorOpen = false
		},

		focusInput(criteriaType: string): void {
			// this.isCriteriaContentOpen = false
			// this.isOntologyButtonClicked = false
			if (criteriaType === 'Einschlusskriterien') {
				this.ausschlussTextSerach = ''
			} else if (criteriaType === 'Ausschlusskriterien') {
				this.einschlussTextSerach = ''
			}
		},

		closeSearchCriteriaModal() {
			this.isCriteriaContentOpen = false
			this.einschlussSearchInput = ''
			this.ausschlussSearchInput = ''
		},

		getSelectedCriteria(items: SelectedCriteriaData): void {
			this.einschlussSearchInput = ''
			this.ausschlussSearchInput = ''
			this.selectedCriteria = items.selectedItems
			this.toggleSearchCriteriaModal(items.criteriaType)
			this.isLimitationsCriteriaOpen = true
		},

		getSelectedFilterInfo(filterInfo: FilterInfo[]) {
			// packt filterInfo in this.selectedCharacteristicsEin with for loop, condition with id and display
			if (this.criteriaOverlayType === 'Einschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) { // when criterion is edited
					this.selectedCharacteristicsEin.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedCharacteristicsEin.push(...filterInfo)
			} else if (this.criteriaOverlayType === 'Ausschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) {
					this.selectedCharacteristicsAus.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedCharacteristicsAus.push(...filterInfo)
			}
		},

		deleteSelectedCriteria(index: number): void {
			this.selectedCriteria?.splice(index, 1)
		},

		deleteCharacteristic(index: number, criteriaType: string): void {
			if (criteriaType === 'Einschlusskriterien') {
				this.selectedCharacteristicsEin.splice(index, 1)
			} else if (criteriaType === 'Ausschlusskriterien') {
				this.selectedCharacteristicsAus.splice(index, 1)
			}
		},

		editCriteriaLimitation(characteristic: FilterInfo, index: number, criteriaType: string): void {
			if (criteriaType === 'Einschlusskriterien') {
				this.selectedEditedCriteria = this.selectedCharacteristicsEin?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = this.selectedEditedCriteria as OntologyTreeElement[]
			} else if (criteriaType === 'Ausschlusskriterien') {
				this.selectedEditedCriteria = this.selectedCharacteristicsAus?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = this.selectedEditedCriteria as OntologyTreeElement[]
			}
			this.selectedEditedCriteriaIndex = index
			this.isLimitationsCriteriaOpen = true
			this.isEditFilterState = true
		},

		updateDivSequence(type: string, newOrder: Array<FilterInfo>) {
			type === 'einschlusskriterien' ? (this.selectedCharacteristicsEin = newOrder) : (this.selectedCharacteristicsAus = newOrder)
		},
	},
})
</script>
<style scoped>
.feasibility-query-builder {
	display: flex;
	flex-direction: column;
	width: 100%;
	/* row-gap: 20px; */
	font-size: 16px;
}

.feasibility-query__input {
	display: flex;
	flex-direction: column;
	width: 100%;
}

.feasibility-query-criteria-wrapper {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	/* margin-bottom: 20px; */
	width: 100%;
}

.criteria {
	flex: 1 1 100%;
	position: relative;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
}

.criteria__title {
	min-width: min-content;
	background-color: #5270a7;
	color: #ffffff;
	text-align: center;
	font-size: 16px;
	font-weight: 500;
	padding: 2px 0;
}

.criteria__search-input {
	flex-direction: row;
	display: flex;
	justify-content: space-around;
	align-items: baseline;
	margin: 10px;
}

.criteria__search-input__button {
	place-content: stretch center;
	align-items: stretch;
	flex-direction: row;
	display: flex;
}

.criteria__search-input__button button {
	border-radius: 4px;
	border-width: 1px;
	border-style: solid;
	height: 44px;
	width: 44px;
	line-height: unset;
	color: white;
	background-color: #5e6c78;
}

.criteria__search-input__button button:hover {
	background-color: #9ea9b3;
}

.criteria__search-input__button button:active {
	background-color: #5e6c78 !important;
	color: white !important;
}

.criteria__search-input__textbox {
	flex: 1 1 100%;
	max-width: 85%;
	font-size: 14px;
}

.pipe {
	flex: 1 1 100%;
	max-width: 1%;
	border-radius: 4px;
	background-color: #5270a7;
}

/* .pipe--criteria {
	border-radius: 4px;
} */

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
	width: 7%;
	background-color: #5270a7;
	padding: 10px 0px;
}

.selected-criteria-display {
	font-weight: 700;
	margin-bottom: 10px;
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

.einschlusscriteria-combining-operator {
	margin-left: 10px;
}

.ausschlusscriteria-combining-operator {
	margin-left: 10px;
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
