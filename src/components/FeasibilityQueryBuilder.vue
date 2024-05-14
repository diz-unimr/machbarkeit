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
				:criteria-id="criteriaOverlay"
				@get-selected-criteria="getSelectedCriteria"
				@toggle-search-criteria="toggleSearchCriteria" />
		</div>

		<LimitationsSelectedFeatures v-if="isCriteriaOptionOpen"
			:selected-ontology-array="selectedOntologyArray"
			@dialog-close="selectionOntologyDiaglogClose"
			@get-filter-info="getFilterInfo" />

		<div class="feasibility-query__output">
			<div class="output-header">
				<p>Ausgewählte Merkmale</p>
			</div>
			<div class="output-content">
				<div id="Einschlusskriterien" class="output-textfield">
					<div v-if="selectedCharacteristics.length > 0">
						<div v-for="(characteristic, index) in selectedCharacteristics" :key="index">
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
				<div id="Ausschlusskriterien" class="output-textfield" />
			</div>
		</div>
	</div>
</template>

<script>
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import SearchTreeOverlayContent from './SearchTreeOverlayContent.vue'
import LimitationsSelectedFeatures from './Limitations/LimitationsSelectedFeatures.vue'

export default {
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		SearchTreeOverlayContent,
		LimitationsSelectedFeatures,
	},

	data() {
		return {
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isCriteriaOptionOpen: false,
			criteriaOverlay: null,
			isCriteriaOverlayOpen: false,
			selectedOntologyArray: null,
			selectedCharacteristics: [],
		}
	},

	computed: {},

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
		toggleSearchCriteria(id) {
			this.criteriaOverlay = id
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
		getSelectedCriteria(selectedOntologyItems) {
			this.toggleSearchCriteria(selectedOntologyItems.criteriaId)
			this.selectionOntologyDialogOpen()
			this.selectedOntologyArray = selectedOntologyItems.items
		},
		getFilterInfo(object) {
			// check
			/* if (this.comparisonRestriction.value === '' || this.comparisonRestriction.min === '' || this.comparisonRestriction.max === '') {
				this.comparisonRestriction.value = 0
				this.comparisonRestriction.min = 0
				this.comparisonRestriction.max = 0
			} */
			this.selectedCharacteristics.push(...object)

		},
	},
}
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
	padding: 10px;
}

.selected-criteria-display {
	font-weight: 700;
}

.selected-criteria-condition span::after {
	content: ',';
}

.selected-criteria-condition span:last-child::after {
	content: none;
}

</style>
