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
			<SearchTreeOverlayContent v-show="criteriaOverlay === 'Einschlusskriterien' || criteriaOverlay === 'Ausschlusskriterien'"
				:criteria-id="criteriaOverlay"
				@get-selected-ontology="getSelectedOntology"
				@toggle-search-criteria="toggleSearchCriteria" />

			<!-- <SearchTreeOverlayContent v-show="isEinschlusskriterienOverlayOpen"
				:response-array="responseArray"
				criteria="Einschlusskriterien"
				@update-array="UpdateArray" />
			<SearchTreeOverlayContent v-show="isAusschlusskriterienOverlayOpen"
				:is-ausschlusskriterien="true"
				:response-array="responseArray"
				criteria="Ausschlusskriterien"
				@update-array="UpdateArray" /> -->
		</div>

		<SelectionOntologyDialog v-if="isOntologyOptionOpen"
			:selected-ontology-array="selectedOntologyArray"
			@dialog-close="selectionOntologyDiaglogClose" />

		<div class="feasibility-query__output">
			<div class="output-header">
				<p>Ausgewählte Merkmale</p>
			</div>
			<div class="output-content">
				<div id="Einschlusskriterien" class="output-textfield">
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
import SelectionOntologyDialog from './SelectionOntologyDialog.vue'

export default {
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		SearchTreeOverlayContent,
		SelectionOntologyDialog,
	},

	data() {
		return {
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isEinschlusskriterienOverlayOpen: false,
			isAusschlusskriterienOverlayOpen: false,
			isOntologyOptionOpen: false,
			responseArray: [],
			filteredArr: [],
			criteriaOverlay: null,
			selectedOntologyArr: [],
			selectedOntologyArray: null,
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
			this.criteriaOverlay = this.criteriaOverlay === id ? null : id
		},
		selectionOntologyDialogOpen() {
			this.isOntologyOptionOpen = true
		},
		selectionOntologyDiaglogClose() {
			this.isOntologyOptionOpen = false
		},
		/* switchSearchEinschlusskriterien() {
			console.log('hello')
			this.isEinschlusskriterienOverlayOpen = !this.isEinschlusskriterienOverlayOpen
			this.isAusschlusskriterienOverlayOpen = false
		},
		switchSearchAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = !this.isAusschlusskriterienOverlayOpen
			this.isEinschlusskriterienOverlayOpen = false
		}, */
		searchCodeEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = true
			this.isAusschlusskriterienOverlayOpen = false
			// this.filteredSearch(this.einschlussTextSerach)
		},
		searchCodeAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = true
			this.isEinschlusskriterienOverlayOpen = false
			// this.filteredSearch(this.ausschlussTextSerach)
		},
		getSelectedOntology(selectedOntologyItems) {
			this.toggleSearchCriteria(selectedOntologyItems.criteriaId)
			this.selectionOntologyDialogOpen()
			this.selectedOntologyArray = selectedOntologyItems.items
		},

		deleteItem(id) {
			// event.target.parentNode.remove()
			const index = this.selectedOntologyArr.findIndex(item => item.id === id)
			this.selectedOntologyArr.splice(index, 1)
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
</style>./SearchTreeOverlayContent.vue
