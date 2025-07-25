<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-builder">
		<div class="feasibility-query__search-input">
			<div class="feasibility-query__search-input-wrapper">
				<div id="inclusionCriteria" class="search-input__title">
					Einschlusskriterien
				</div>
				<div>
					<div class="search-input__body" :style="{marginBottom: requestWarning !== '' ? '0px' : '10px'}">
						<button id="inclusionCriteria" @click="toggleOntologyPanel('inclusionCriteria')">
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
							@input="onInput('inclusionCriteria')"
							@trailing-button-click="closeOntologyPanel">
							<Magnify :size="20" />
						</NcTextField>
					</div>
					<div v-if="inclusionSearchInputText !== '' && requestWarning !== ''" class="search-input-warning">
						<span>{{ requestWarning }}</span>
					</div>
				</div>
			</div>
			<!-- <div class="pipe" />
			<div class="feasibility-query__search-input-wrapper">
				<div id="exclusionCriteria" class="search-input__title">
					Ausschlusskriterien
				</div>
				<div>
					<div class="search-input__body" :style="{marginBottom: requestWarning !== '' ? '0px' : '10px'}">
						<button id="exclusionCriteria" @click="toggleOntologyPanel('exclusionCriteria')">
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
							@focus="focusSearchInput('exclusionCriteria')"
							@trailing-button-click="closeOntologyPanel">
							<Magnify :size="20" />
						</NcTextField>
					</div>
					<div v-if="exclusionSearchInputText !== '' && requestWarning !== ''" class="search-input-warning">
						{{ requestWarning }}
					</div>
				</div>
			</div> -->
		</div>

		<OntologyPanel v-if="isOntologyPanelOpen"
			:search-input-text="searchInputText"
			:is-input-text-cleared="isInputTextCleared"
			:criteria-type="criteriaOverlayType"
			@submit-selected-criteria="submitSelectedCriteria"
			@toggle-ontology-panel="toggleOntologyPanel"
			@close-ontology-panel="closeOntologyPanel"
			@send-request-warning="getRequestWarning" />

		<FilterPanel v-if="isFilterPanelOpen"
			:is-state-edit-filter="isStateEditFilter"
			:edit-filter-information="editFilterInformation"
			:criteria-type="criteriaOverlayType"
			@dialog-close="isFilterPanelOpen = false" />

		<FeasibilityQueryDisplay @edit-filter="editFilter" />
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import OntologyPanel from './OntologyPanel.vue'
import FilterPanel from './filters/FilterPanel.vue'
import FeasibilityQueryDisplay from './FeasibilityQueryDisplay.vue'
import type { FeasibilityQueryBuilderData } from '../types/FeasibilityQueryBuilderData'
import debounce from 'lodash.debounce'

export default Vue.extend({
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		OntologyPanel,
		FilterPanel,
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
	},

	data(): FeasibilityQueryBuilderData {
		return {
			inclusionSearchInputText: '',
			exclusionSearchInputText: '',
			searchInputText: '',
			isFilterPanelOpen: false,
			isOntologyPanelOpen: false,
			criteriaOverlayType: '',
			requestWarning: '',
			isStateEditFilter: false,
			editFilterInformation: undefined,
			isInputTextCleared: false,
			imgDelete: './img/delete-black.png',
		}
	},

	watch: {
		isSaveModalOpen(value) {
			if (value) {
				this.isOntologyPanelOpen = false
				this.isFilterPanelOpen = false
				this.$store.dispatch('clearCheckedItems')
				this.$store.dispatch('clearSelectedItems')
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
		onInput(criteriaType: string): void {
			this.criteriaOverlayType = criteriaType
			this.requestWarning = ''
			if (this.inclusionSearchInputText.length === 0) {
				this.searchInputText = ''
				if (this.isOntologyPanelOpen === false) this.isOntologyPanelOpen = true
			} else this.checkTextInputLengthdebounce()
		},

		checkTextInputLengthdebounce: debounce(function(): void {
			this.isOntologyPanelOpen = true
			this.searchInputText = this.inclusionSearchInputText
		}, 1000),

		clearOntologyInput(): void {
			this.inclusionSearchInputText = ''
			this.exclusionSearchInputText = ''
			this.searchInputText = ''
			this.requestWarning = ''
		},

		toggleOntologyPanel(criteriaType: string): void {
			this.clearOntologyInput()
			this.criteriaOverlayType = criteriaType
			this.isOntologyPanelOpen = !this.isOntologyPanelOpen
		},

		closeOntologyPanel() {
			this.clearOntologyInput()
			this.isInputTextCleared = true
			this.isOntologyPanelOpen = false
			// wait for vue update then reset isInputTextCleared = false
			this.$nextTick(() => {
				this.isInputTextCleared = false
			})
		},

		getRequestWarning(requestWarning: string): void {
			this.requestWarning = requestWarning
			if (this.requestWarning !== '') this.isOntologyPanelOpen = false
		},

		/* focusSearchInput(criteriaType: string): void {
			if (this.criteriaOverlayType !== criteriaType) {
				// this.searchInputText = ''
				if (criteriaType === 'inclusionCriteria') {
					this.exclusionSearchInputText = ''
				} else if (criteriaType === 'exclusionCriteria') {
					this.inclusionSearchInputText = ''
				}
			}
		}, */

		submitSelectedCriteria(): void {
			this.closeOntologyPanel()
			this.isStateEditFilter = false
			this.isFilterPanelOpen = true
		},

		editFilter(characteristicId: string, index: number, criteriaType: string): void {
			this.isFilterPanelOpen = true
			this.isStateEditFilter = true
			this.editFilterInformation = {
				id: characteristicId,
				index,
				criteriaType,
			}
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
	max-width: 90%;
	font-size: 15px;
}

.text-field-wrapper {
  display: flex;
  flex-direction: column;
  max-width: 300px;
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.input-with-icon input {
  width: 100%;
  padding: 6px 28px;
  font-size: 14px;
}

.input-with-icon .icon {
  position: absolute;
  left: 6px;
  pointer-events: none;
}

.clear-button {
  position: absolute;
  right: 6px;
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
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
