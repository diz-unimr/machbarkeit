<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-builder">
		<div class="feasibility-query__search-input">
			<div class="feasibility-query__search-input-wrapper">
				<div class="search-input__title">
					Einschlusskriterien
				</div>
				<div class="search-input__body">
					<button id="einschlusskriterien" @click="toggleOntologySearchTreeModal('einschlusskriterien')">
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
					<NcTextField :value.sync="inclusionSearchInputTemp"
						label="Code oder Suchbegriff eingeben"
						trailing-button-icon="close"
						placeholder=" "
						:show-trailing-button="inclusionSearchInputTemp !== ''"
						@trailing-button-click="closeOntologySearchTreeModal"
						@focus="focusSearchInput('einschlusskriterien')">
						<Magnify :size="20" />
					</NcTextField>
				</div>
			</div>
			<div class="pipe" />
			<div class="feasibility-query__search-input-wrapper">
				<div class="search-input__title">
					Ausschlusskriterien
				</div>
				<div class="search-input__body">
					<button id="ausschlusskriterien" @click="toggleOntologySearchTreeModal('ausschlusskriterien')">
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
					<NcTextField :value.sync="exclusionSearchInputTemp"
						label="Code oder Suchbegriff eingeben"
						trailing-button-icon="close"
						placeholder=" "
						:show-trailing-button="exclusionSearchInputTemp !== ''"
						@trailing-button-click="closeOntologySearchTreeModal"
						@focus="focusSearchInput('ausschlusskriterien')">
						<Magnify :size="20" />
					</NcTextField>
				</div>
			</div>
		</div>

		<OntologySearchTreeModal v-if="isOntologySearchTreeOpen"
			:criteria-type="criteriaOverlayType"
			:inclusion-search-input="inclusionSearchInput"
			:exclusion-search-input="exclusionSearchInput"
			@get-selected-criteria="getSelectedCriteria"
			@toggle-ontology-search-tree-modal="toggleOntologySearchTreeModal" />

		<LimitationsSelectedCriteriaModal v-if="isLimitationsCriteriaOpen"
			:selected-criteria="selectedCriteria"
			:ui-profile="uiProfile"
			:is-state-edit-filter="isStateEditFilter"
			@get-selected-filter-info="getSelectedFilterInfo"
			@dialog-close="isLimitationsCriteriaOpen = false"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<FeasibilityQueryDisplay :selected-inclusion-characteristics="selectedInclusionCharacteristics"
			:selected-exclusion-characteristics="selectedExclusionCharacteristics"
			@update-display-sequence="updateDisplaySequence"
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
import FeasibilityQueryDisplay from './FeasibilityQueryDisplay.vue'
import type { FeasibilityQueryBuilderData } from '../types/FeasibilityQueryBuilderData'
import type { FilterInfo } from '../types/LimitationsSelectedCriteriaCardData.ts'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData.ts'
import debounce from 'lodash.debounce'

export default Vue.extend({
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		OntologySearchTreeModal,
		LimitationsSelectedCriteriaModal,
		FeasibilityQueryDisplay,
	},
	props: {
		isSaveModalOpen: {
			type: Boolean,
			required: true,
		},

		enableStartQueryButton: {
			type: Function,
			default: () => {},
		},
	},

	data(): FeasibilityQueryBuilderData {
		return {
			uiProfile: null,
			inclusionSearchInputTemp: '',
			exclusionSearchInputTemp: '',
			inclusionSearchInput: '',
			exclusionSearchInput: '',
			isLimitationsCriteriaOpen: false,
			isOntologySearchTreeOpen: false,
			criteriaOverlayType: '',
			selectedCriteria: null,
			selectedEditedCriteria: null,
			selectedEditedCriteriaIndex: null,
			selectedInclusionCharacteristics: [],
			selectedExclusionCharacteristics: [],
			isStateEditFilter: false,
			debouncedHandler: null,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete-black.png',
		}
	},

	computed: {
		hasCharacteristics(): boolean {
			return this.selectedInclusionCharacteristics.length > 0 || this.selectedExclusionCharacteristics.length > 0
		},
	},

	watch: {
		isSaveModalOpen(value) {
			if (value === true) this.isOntologySearchTreeOpen = false
		},

		hasCharacteristics(hasValue) {
			if (hasValue) {
				this.$emit('enable-start-query-button', true)
			} else this.$emit('enable-start-query-button', false)
		},

		inclusionSearchInputTemp(newVal) {
			if (newVal.length <= 0) {
				this.inclusionSearchInput = this.inclusionSearchInputTemp
				this.isOntologySearchTreeOpen = false
				/* if (this.debouncedHandler?.cancel) {
					this.debouncedHandler.cancel()
				} */
			} else {
				this.debouncedHandler = debounce(() => {
					this.inclusionSearchInput = this.inclusionSearchInputTemp
					this.criteriaOverlayType = 'einschlusskriterien'
					if (this.inclusionSearchInputTemp.length > 0) {
						this.isOntologySearchTreeOpen = true
					}
				}, 500)
				this.debouncedHandler()
			}
		},

		exclusionSearchInputTemp(newVal) {
			if (newVal.length <= 0) {
				this.exclusionSearchInput = this.exclusionSearchInputTemp
				this.isOntologySearchTreeOpen = false
				/* if (this.debouncedHandler?.cancel) {
					this.debouncedHandler.cancel()
				} */
			} else {
				this.debouncedHandler = debounce(() => {
					this.exclusionSearchInput = this.exclusionSearchInputTemp
					this.criteriaOverlayType = 'ausschlusskriterien'
					if (this.exclusionSearchInputTemp.length > 0) {
						this.isOntologySearchTreeOpen = true
					}
				}, 500)
				this.debouncedHandler()
			}
		},
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
			try {
				const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ui_profile'))
				this.uiProfile = response.data
			} catch (error) {
				// eslint-disable-next-line no-console
				console.log((error as Error).message)
			}
		},

		toggleOntologySearchTreeModal(type: string): void {
			this.criteriaOverlayType = type
			this.isOntologySearchTreeOpen = !this.isOntologySearchTreeOpen
			this.inclusionSearchInputTemp = ''
			this.exclusionSearchInputTemp = ''
		},

		focusSearchInput(criteriaType: string): void {
			if (criteriaType === 'einschlusskriterien') {
				this.exclusionSearchInput = ''
				this.exclusionSearchInputTemp = ''
			} else if (criteriaType === 'ausschlusskriterien') {
				this.inclusionSearchInput = ''
				this.inclusionSearchInputTemp = ''
			}
		},

		closeOntologySearchTreeModal() {
			this.isOntologySearchTreeOpen = false
			this.inclusionSearchInputTemp = ''
			this.exclusionSearchInputTemp = ''
		},

		getSelectedCriteria(criteriaType: string, items: OntologyTreeElement[]): void {
			this.inclusionSearchInputTemp = ''
			this.exclusionSearchInputTemp = ''
			this.selectedCriteria = items
			this.toggleOntologySearchTreeModal(criteriaType)
			this.isLimitationsCriteriaOpen = true
		},

		getSelectedFilterInfo(filterInfo: FilterInfo[]) {
			// put filterInfo into this.selectedInclusionCharacteristics with for loop, condition with id and display
			if (this.criteriaOverlayType === 'einschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) { // when criterion is edited
					this.selectedInclusionCharacteristics.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedInclusionCharacteristics.push(...filterInfo)
			} else if (this.criteriaOverlayType === 'ausschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) {
					this.selectedExclusionCharacteristics.splice(this.selectedEditedCriteriaIndex, 1, ...filterInfo)
					this.selectedEditedCriteriaIndex = null
				} else this.selectedExclusionCharacteristics.push(...filterInfo)
			}
		},

		deleteSelectedCriteria(index: number): void {
			this.selectedCriteria?.splice(index, 1)
		},

		deleteCharacteristic(index: number, criteriaType: string): void {
			if (criteriaType === 'einschlusskriterien') {
				this.selectedInclusionCharacteristics.splice(index, 1)
			} else if (criteriaType === 'ausschlusskriterien') {
				this.selectedExclusionCharacteristics.splice(index, 1)
			}
		},

		editCriteriaLimitation(characteristic: FilterInfo, index: number, criteriaType: string): void {
			this.criteriaOverlayType = criteriaType
			if (criteriaType === 'einschlusskriterien') {
				this.selectedEditedCriteria = this.selectedInclusionCharacteristics?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = this.selectedEditedCriteria as OntologyTreeElement[]
			} else if (criteriaType === 'ausschlusskriterien') {
				this.selectedEditedCriteria = this.selectedExclusionCharacteristics?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = this.selectedEditedCriteria as OntologyTreeElement[]
			}
			this.selectedEditedCriteriaIndex = index
			this.isLimitationsCriteriaOpen = true
			this.isStateEditFilter = true
		},

		updateDisplaySequence(type: string, newOrder: Array<FilterInfo>) {
			type === 'einschlusskriterien' ? (this.selectedInclusionCharacteristics = newOrder) : (this.selectedExclusionCharacteristics = newOrder)
		},
	},
})
</script>
<style scoped>
.feasibility-query-builder {
	display: flex;
	flex-direction: column;
	width: 100%;
}

.feasibility-query__search-input {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	width: 100%;
}

.feasibility-query__search-input-wrapper {
	flex: 1 1 100%;
	position: relative;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
}

.search-input__title {
	min-width: min-content;
	background-color: #5270a7;
	color: #ffffff;
	text-align: center;
	font-weight: 500;
	padding: 2px 0;
}

.search-input__body {
	flex-direction: row;
	display: flex;
	justify-content: space-around;
	align-items: baseline;
	margin: 10px;
}

.search-input__body button {
	border-radius: 5px;
	border-width: 1px;
	border-style: solid;
	height: 44px;
	width: 44px;
	line-height: unset;
	color: white;
	background-color: #5e6c78;
}

.search-input__body button:hover {
	background-color: #9ea9b3;
}

.search-input__body button:active {
	background-color: #5e6c78 !important;
	color: white !important;
}

.search-input__body .input-field {
	max-width: 85%;
	font-size: 15px;
}

.pipe {
	flex: 1 1 100%;
	max-width: 1%;
	border-radius: 4px;
	background-color: #5270a7;
}
</style>
