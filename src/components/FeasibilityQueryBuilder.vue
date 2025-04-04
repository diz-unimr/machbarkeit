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
				<div>
					<div class="search-input__body" :style="{marginBottom: searchInputWarning !== '' ? '0px' : '10px'}">
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
						<NcTextField :value.sync="inclusionSearchInputText"
							label="Code oder Suchbegriff eingeben"
							trailing-button-icon="close"
							placeholder=" "
							:show-trailing-button="inclusionSearchInputText !== ''"
							@trailing-button-click="closeOntologySearchTreeModal"
							@focus="focusSearchInput('einschlusskriterien')">
							<Magnify :size="20" />
						</NcTextField>
					</div>
					<div v-if="inclusionSearchInputText !== '' && searchInputWarning !== ''" class="search-input-warning">
						{{ searchInputWarning }}
					</div>
				</div>
			</div>
			<div class="pipe" />
			<div class="feasibility-query__search-input-wrapper">
				<div class="search-input__title">
					Ausschlusskriterien
				</div>
				<div>
					<div class="search-input__body" :style="{marginBottom: searchInputWarning !== '' ? '0px' : '10px'}">
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
						<NcTextField :value.sync="exclusionSearchInputText"
							label="Code oder Suchbegriff eingeben"
							trailing-button-icon="close"
							placeholder=" "
							:show-trailing-button="exclusionSearchInputText !== ''"
							@trailing-button-click="closeOntologySearchTreeModal"
							@focus="focusSearchInput('ausschlusskriterien')">
							<Magnify :size="20" />
						</NcTextField>
					</div>
					<div v-if="exclusionSearchInputText !== '' && searchInputWarning !== ''" class="search-input-warning">
						{{ searchInputWarning }}
					</div>
				</div>
			</div>
		</div>

		<OntologySearchTreeModal v-if="isOntologySearchTreeOpen"
			:criteria-type="criteriaOverlayType"
			:search-input-text="searchInputText"
			@get-selected-criteria="getSelectedCriteria"
			@update-modules="HandleModules"
			@get-request-status="getRequestStatus"
			@toggle-ontology-search-tree-modal="toggleOntologySearchTreeModal" />

		<LimitationsSelectedCriteriaModal v-if="isLimitationsCriteriaOpen"
			:selected-criteria="selectedCriteria"
			:is-state-edit-filter="isStateEditFilter"
			@get-selected-filter-info="getSelectedFilterInfo"
			@dialog-close="isLimitationsCriteriaOpen = false"
			@delete-selected-criteria="deleteSelectedCriteria" />

		<FeasibilityQueryDisplay :selected-inclusion-characteristics="selectedInclusionCharacteristics"
			:selected-exclusion-characteristics="selectedExclusionCharacteristics"
			@update-characteristics="updateCharacteristics"
			@edit-criteria-limitation="editCriteriaLimitation"
			@delete-characteristic="deleteCharacteristic" />
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import OntologySearchTreeModal from './OntologySearchTreeModal.vue'
import LimitationsSelectedCriteriaModal from './Limitations/LimitationsSelectedCriteriaModal.vue'
import FeasibilityQueryDisplay from './FeasibilityQueryDisplay.vue'
import type { FeasibilityQueryBuilderData, SelectedCharacteristics, QueryCriterionData } from '../types/FeasibilityQueryBuilderData'
import type { Criterion, Modules } from '../types/OntologySearchTreeModalData.ts'
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRangeType } from '../types/TimeRangeOptionsData'

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
		isCriteriaAvailable: {
			type: Boolean,
			default: false,
		},

		isSaveModalOpen: {
			type: Boolean,
			required: true,
		},

		dataFromUpload: {
			type: Object as PropType<{ inclusionCharacteristics: SelectedCharacteristics, exclusionCharacteristics: SelectedCharacteristics }>,
			default: null,
		},
	},

	data(): FeasibilityQueryBuilderData {
		return {
			modules: null,
			inclusionSearchInputText: '',
			exclusionSearchInputText: '',
			searchInputText: '',
			isLimitationsCriteriaOpen: false,
			isOntologySearchTreeOpen: false,
			criteriaOverlayType: '',
			selectedCriteria: null,
			selectedEditedCriteriaIndex: null,
			selectedInclusionCharacteristics: {
				characteristics: [],
				logic: [],
			},
			selectedExclusionCharacteristics: {
				characteristics: [],
				logic: [],
			},
			queryData: {
				version: '',
				display: '',
			},
			isStateEditFilter: false,
			debouncedHandler: null,
			searchInputWarning: '',
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete-black.png',
		}
	},

	watch: {
		dataFromUpload(data) {
			if (data) {
				this.selectedInclusionCharacteristics = data.inclusionCharacteristics
				this.selectedExclusionCharacteristics = data.exclusionCharacteristics
			}
		},

		isCriteriaAvailable(value) {
			if (!value) {
				this.selectedInclusionCharacteristics = {
					characteristics: [],
					logic: [],
				}
				this.selectedExclusionCharacteristics = {
					characteristics: [],
					logic: [],
				}
				this.updateQueryData(this.selectedInclusionCharacteristics, this.selectedExclusionCharacteristics)
			}
		},

		isSaveModalOpen(value) {
			if (value) this.isOntologySearchTreeOpen = false
		},

		inclusionSearchInputText(newVal) {
			this.searchInputText = newVal
			if (newVal.length === 0) {
				this.isOntologySearchTreeOpen = false
				this.searchInputWarning = ''
			} /* else if (newVal.length < 2) {
				this.isOntologySearchTreeOpen = false
				this.searchInputWarning = 'Bitte mindestens 2 Buchstaben eingeben'
			} */ else {
				this.searchInputWarning = ''
				this.debouncedHandler = debounce(() => {
					this.criteriaOverlayType = 'einschlusskriterien'
					this.isOntologySearchTreeOpen = true
				}, 1000)
				this.debouncedHandler()
			}
		},

		exclusionSearchInputText(newVal) {
			this.searchInputText = this.exclusionSearchInputText
			if (newVal.length === 0) {
				this.isOntologySearchTreeOpen = false
				this.searchInputWarning = ''
			}/* else if (newVal.length < 2) {
				this.isOntologySearchTreeOpen = false
				this.searchInputWarning = 'Bitte mindestens 2 Buchstaben eingeben'
			} */ else {
				this.searchInputWarning = ''
				this.debouncedHandler = debounce(() => {
					this.criteriaOverlayType = 'ausschlusskriterien'
					this.isOntologySearchTreeOpen = true
				}, 1000)
				this.debouncedHandler()
			}
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
		toggleOntologySearchTreeModal(type: string): void {
			this.criteriaOverlayType = type
			this.isOntologySearchTreeOpen = !this.isOntologySearchTreeOpen
			this.inclusionSearchInputText = ''
			this.exclusionSearchInputText = ''
			this.searchInputText = ''
		},

		closeOntologySearchTreeModal() {
			this.isOntologySearchTreeOpen = false
			this.inclusionSearchInputText = ''
			this.exclusionSearchInputText = ''
			this.searchInputText = ''
		},

		focusSearchInput(criteriaType: string): void {
			this.isOntologySearchTreeOpen = false
			if (criteriaType === 'einschlusskriterien') {
				this.exclusionSearchInputText = ''
				this.searchInputText = ''
			} else if (criteriaType === 'ausschlusskriterien') {
				this.inclusionSearchInputText = ''
				this.searchInputText = ''
			}
		},

		HandleModules(modules: Array<Modules>): void {
			this.modules = modules
		},

		getRequestStatus(status: number, message: string | undefined): void {
			if (status === 200) {
				this.searchInputWarning = ''
			} else if (status === 400) {
				this.searchInputWarning = 'Bitte mindestens 2 Buchstaben eingeben'
			} else if (status === 500) {
				this.searchInputWarning = ''
				alert(message)
			}
		},

		getSelectedCriteria(criteriaType: string, items: Criterion[]): void {
			this.inclusionSearchInputText = ''
			this.exclusionSearchInputText = ''
			this.searchInputText = ''
			this.selectedCriteria = items
			this.toggleOntologySearchTreeModal(criteriaType)
			this.isLimitationsCriteriaOpen = true
		},

		getSelectedFilterInfo(filterInfo: Array<ConceptType | QuantityType | TimeRangeType>) {
			this.selectedCriteria = this.selectedCriteria!.map((item, index) => {
				if (filterInfo[index]) {
					item.selectedFilter = filterInfo[index] as ConceptType | QuantityType | TimeRangeType
				} else {
					item.selectedFilter = undefined
				}
				return item
			})

			// put filterInfo into this.selectedInclusionCharacteristics with for loop, condition with id and display
			if (this.criteriaOverlayType === 'einschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) { // when criterion is edited
					this.selectedInclusionCharacteristics.characteristics.splice(this.selectedEditedCriteriaIndex, 1, ...this.selectedCriteria)
					this.selectedEditedCriteriaIndex = null
				} else {
					this.selectedInclusionCharacteristics.characteristics.push(...this.selectedCriteria)
					const length = this.selectedInclusionCharacteristics.characteristics.length === 0 ? this.selectedCriteria.length - 1 : this.selectedCriteria.length
					this.selectedInclusionCharacteristics.logic.push(...Array.from({ length }, () => 'and'))
				}
			} else if (this.criteriaOverlayType === 'ausschlusskriterien') {
				if (this.selectedEditedCriteriaIndex !== null) {
					this.selectedExclusionCharacteristics.characteristics.splice(this.selectedEditedCriteriaIndex, 1, ...this.selectedCriteria)
					this.selectedEditedCriteriaIndex = null
				} else {
					this.selectedExclusionCharacteristics.characteristics.push(...this.selectedCriteria)
					const length = this.selectedInclusionCharacteristics.characteristics.length === 0 ? this.selectedCriteria.length - 1 : this.selectedCriteria.length
					this.selectedExclusionCharacteristics.logic.push(...Array.from({ length }, () => 'and'))
				}
			}
			this.updateQueryData(this.selectedInclusionCharacteristics, this.selectedExclusionCharacteristics)
		},

		deleteSelectedCriteria(index: number): void {
			this.selectedCriteria?.splice(index, 1)
		},

		deleteCharacteristic(index: number, criteriaType: string): void {
			if (criteriaType === 'einschlusskriterien') {
				this.selectedInclusionCharacteristics.characteristics.splice(index, 1)
			} else if (criteriaType === 'ausschlusskriterien') {
				this.selectedExclusionCharacteristics.characteristics.splice(index, 1)
			}
		},

		editCriteriaLimitation(characteristic: Criterion, index: number, criteriaType: string): void {
			this.criteriaOverlayType = criteriaType
			if (criteriaType === 'einschlusskriterien') {
				const selectedEditedCriteria = this.selectedInclusionCharacteristics.characteristics?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = selectedEditedCriteria as Criterion[]
			} else if (criteriaType === 'ausschlusskriterien') {
				const selectedEditedCriteria = this.selectedExclusionCharacteristics.characteristics?.filter((item, itemIndex) => (item.id === characteristic.id) && (itemIndex === index))
				this.selectedCriteria = selectedEditedCriteria as Criterion[]
			}
			this.selectedEditedCriteriaIndex = index
			this.isLimitationsCriteriaOpen = true
			this.isStateEditFilter = true
		},

		updateCharacteristics(type: string, newOrder: SelectedCharacteristics) {
			type === 'einschlusskriterien' ? (this.selectedInclusionCharacteristics = { ...newOrder }) : (this.selectedExclusionCharacteristics = { ...newOrder })
			this.updateQueryData(this.selectedInclusionCharacteristics, this.selectedExclusionCharacteristics)
		},

		updateQueryData(selectedIncludeCriteria: SelectedCharacteristics, selectedExcludeCriteria: SelectedCharacteristics) {
			// TODO: update Context Here
			this.queryData.inclusionCriteria = []
			this.queryData.exclusionCriteria = []
			if (selectedIncludeCriteria.characteristics.length > 0) {
				let tempIndex = 0
				for (let i = 0; i < selectedIncludeCriteria.characteristics.length; i++) {
					const module = this.modules?.find(module => module.id === selectedIncludeCriteria.characteristics[i].moduleId)
					const selectedCharacteristic = {
						id: selectedIncludeCriteria.characteristics[i].id,
						termCodes: selectedIncludeCriteria.characteristics[i].termCodes,
						// context: selectedIncludeCriteria.characteristics[i].context,
						context: {
							code: module?.fdpgCdsCode,
							display: module?.name,
							system: module?.fdpgCdsSystem,
							version: module?.fdpgCdsVersion,
						},
						...(selectedIncludeCriteria.characteristics[i].selectedFilter || {}),
					} as QueryCriterionData

					if (i === 0) {
						this.queryData.inclusionCriteria.push([selectedCharacteristic])
					} else {
						if (selectedIncludeCriteria.logic[i - 1] === 'or') {
							this.queryData.inclusionCriteria[tempIndex].push(selectedCharacteristic)
						} else if (selectedIncludeCriteria.logic[i - 1] === 'and') {
							this.queryData.inclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						}
					}

				}
			}

			if (selectedExcludeCriteria.characteristics.length > 0) {
				let tempIndex = 0
				for (let i = 0; i < selectedExcludeCriteria.characteristics.length; i++) {
					const module = this.modules?.find(module => module.id === selectedExcludeCriteria.characteristics[i].moduleId)
					const selectedCharacteristic = {
						id: selectedExcludeCriteria.characteristics[i].id,
						termCodes: selectedExcludeCriteria.characteristics[i].termCodes,
						// context: selectedExcludeCriteria.characteristics[i].context,
						context: {
							code: module?.fdpgCdsCode,
							display: module?.name,
							system: module?.fdpgCdsSystem,
							version: module?.fdpgCdsVersion,
						},
						...(selectedExcludeCriteria.characteristics[i].selectedFilter || {}),
					} as QueryCriterionData
					if (i === 0) {
						this.queryData.exclusionCriteria.push([selectedCharacteristic])
					} else {
						if (selectedExcludeCriteria.logic[i - 1] === 'or') {
							this.queryData.exclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						} else if (selectedExcludeCriteria.logic[i - 1] === 'and') {
							this.queryData.exclusionCriteria[tempIndex].push(selectedCharacteristic)
						}
					}

				}
			}

			this.queryData.inclusionCriteria?.length === 0 && this.queryData.exclusionCriteria?.length === 0
				? this.$emit('get-query-data', null)
				: this.$emit('get-query-data', this.queryData)
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

.search-input-warning {
	display: flex;
	padding-left: 15%;
	font-size: 14px;
	color: red;
}

.pipe {
	flex: 1 1 100%;
	max-width: 1%;
	border-radius: 4px;
	background-color: #5270a7;
}
</style>
