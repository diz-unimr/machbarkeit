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
								@focus="focusInput('Einschlusskriterien')">
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
				@get-selected-criteria="getSelectedCriteria"
				@toggle-search-criteria="toggleSearchCriteria" />
		</div>

		<LimitationsSelectedCriteriaModal v-if="isCriteriaOptionOpen"
			:selected-criteria="selectedCriteria"
			:ui-profile="uiProfile"
			@dialog-close="selectionOntologyDiaglogClose"
			@get-filter-info="getFilterInfo"
			@update-selected-criteria="updateSelectedCriteria"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<LimitationsCriterionEditorModal v-if="isCriteriaOptionEditorOpen"
			:selected-edited-criteria="selectedEditedCriteria"
			:ui-profile="uiProfile"
			@dialog-close="selectionOntologyDiaglogClose"
			@get-filter-info="getFilterInfo"
			@update-edited-criteria="updateEditedCriteria"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<FeasibilityQueryDisplay :selected-characteristics-ein="selectedCharacteristicsEin"
			:selected-characteristics-aus="selectedCharacteristicsAus"
			@edit-criteria-limitation="editCriteriaLimitation"
			@delete-characteristic="deleteCharacteristic" />
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
import LimitationsCriterionEditorModal from './Limitations/LimitationsCriterionEditorModal.vue'
import FeasibilityQueryDisplay from './FeasibilityQueryDisplay.vue'
import type { FilterInfo, FeasibilityQueryBuilderData } from '../types/FeasibilityQueryBuilderData'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData.ts'
import type { ConceptType, QuantityType, TimeRange } from '../types/LimitationsSelectedCriteriaModalData.ts'

interface SelectedCriteriaData {
	criteriaType: string;
	selectedItems: OntologyTreeElement[];
}

interface updatedCriteriaData {
	id: string | number;
	item: ConceptType | QuantityType | TimeRange;
}

export default Vue.extend({
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		OntologySearchTreeModal,
		LimitationsSelectedCriteriaModal,
		LimitationsCriterionEditorModal,
		FeasibilityQueryDisplay,
	},

	data(): FeasibilityQueryBuilderData {
		return {
			uiProfile: null,
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isCriteriaOptionOpen: false,
			isCriteriaOptionEditorOpen: false,
			isCriteriaContentOpen: false,
			criteriaOverlayType: '',
			selectedCriteria: null,
			selectedEditedCriteria: null,
			selectedEditedCriteriaIndex: null,
			selectedCharacteristicsEin: [],
			selectedCharacteristicsAus: [],
			btnLastChild: null,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete-black.png',
		}
	},

	watch: {
		einschlussTextSerach() {
			if (this.einschlussTextSerach.length > 0) {
				this.criteriaOverlayType = 'Einschlusskriterien'
				this.isCriteriaContentOpen = true
			} else this.isCriteriaContentOpen = false
		},

		ausschlussTextSerach() {
			if (this.ausschlussTextSerach.length > 0) {
				this.criteriaOverlayType = 'Ausschlusskriterien'
				this.isCriteriaContentOpen = true
			} else this.isCriteriaContentOpen = false
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getUiProfile()
		console.log('this.selectedCriteria: ', this.selectedCriteria)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
		console.log('this.selectedCriteria: ', this.selectedCriteria)
		/* if (document.getElementsByClassName('einschlusscriteria-combining-operator').length > 0) {
			console.log('update')
			const buttonElements = document.getElementsByClassName('einschlusscriteria-combining-operator') as HTMLCollectionOf<HTMLElement>
			// const lastButton = buttonElements.length - 1
			if (this.firstAusschlussCriteria === true) {
				buttonElements[0].style.display = 'none'
			}

		}
		console.log(document.getElementsByClassName('ausschlusscriteria-combining-operator').length)
		if (document.getElementsByClassName('ausschlusscriteria-combining-operator').length > 0) {
			const buttonElements = document.getElementsByClassName('ausschlusscriteria-combining-operator') as HTMLCollectionOf<HTMLElement>
			const lastButton = buttonElements.length - 1
			console.log('lastButton Aus: ', lastButton)
			buttonElements[lastButton].style.display = 'none'
		} */
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getUiProfile(): Promise<void> {
			const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ui_profile'))
			this.uiProfile = response.data
		},

		toggleSearchCriteria(type: string): void {
			this.criteriaOverlayType = type
			this.isCriteriaContentOpen = !this.isCriteriaContentOpen
			this.einschlussTextSerach = ''
			this.ausschlussTextSerach = ''
		},

		selectionOntologyDialogOpen(): void {
			this.isCriteriaOptionOpen = true
		},

		selectionOntologyDiaglogClose(): void {
			this.isCriteriaOptionOpen = false
			this.isCriteriaOptionEditorOpen = false
		},

		focusInput(criteriaType: string): void {
			if (criteriaType === 'Einschlusskriterien') {
				this.ausschlussTextSerach = ''
			} else if (criteriaType === 'Ausschlusskriterien') {
				this.einschlussTextSerach = ''
			}
		},

		getSelectedCriteria(items: SelectedCriteriaData): void {
			this.einschlussTextSerach = ''
			this.ausschlussTextSerach = ''
			this.toggleSearchCriteria(items.criteriaType)
			this.selectionOntologyDialogOpen()
			this.selectedCriteria = items.selectedItems
		},

		getFilterInfo(filterInfo: FilterInfo[]): void {
			if (this.criteriaOverlayType === 'Einschlusskriterien') {
				console.log('this.selectedEditedCriteriaIndex: ', this.selectedEditedCriteriaIndex)
				if (this.selectedEditedCriteriaIndex !== null) {
					console.log(...filterInfo)
					console.log('this.selectedEditedCriteriaIndex AGAIN: ', this.selectedEditedCriteriaIndex)
					this.selectedCharacteristicsEin.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedCharacteristicsEin.push(...filterInfo)
			} else if (this.criteriaOverlayType === 'Ausschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) {
					this.selectedCharacteristicsAus.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedCharacteristicsAus.push(...filterInfo)
			}

			console.log('this.selectedCharacteristicsEin: ', this.selectedCharacteristicsEin)
		},

		updateSelectedCriteria(data: updatedCriteriaData): void {
			console.log('data: ', data)
			this.selectedCriteria![data.id][data.item.type] = data.item
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

		editCriteriaLimitation(characteristic: FilterInfo, index: number): void {
			console.log('this.selectedCriteria: ', this.selectedCriteria)
			this.selectedEditedCriteria = this.selectedCriteria?.filter((item) => item.id === characteristic.id) as OntologyTreeElement[]
			this.selectedEditedCriteriaIndex = index
			console.log('this.selectedEditedCriteria: ', this.selectedEditedCriteria)
			this.isCriteriaOptionEditorOpen = true
		},

		updateEditedCriteria(data: updatedCriteriaData): void {
			console.log('data: ', data)
			/* console.log('this.selectedEditedCriteria: ', this.selectedEditedCriteria)
			this.selectedEditedCriteria![data.id][data.item.type] = data.item
			console.log('this.selectedEditedCriteria: ', this.selectedEditedCriteria) */
			this.selectedCriteria![this.selectedEditedCriteriaIndex!][data.item.type] = data.item
			console.log('this.selectedCriteria: ', this.selectedCriteria)
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
