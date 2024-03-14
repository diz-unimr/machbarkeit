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
							<button @click="switchSearchEinschlusskriterien">
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
							<button @click="switchSearchAusschlusskriterien">
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
			<SearchTreeOverlayContent v-if="isEinschlusskriterienOverlayOpen"
				:filtered-arr="filteredArr"
				criteria="Einschlusskriterien" />
			<SearchTreeOverlayContent v-if="isAusschlusskriterienOverlayOpen"
				:is-ausschlusskriterien="isAusschlusskriterien"
				:filtered-arr="filteredArr"
				criteria="Ausschlusskriterien" />
		</div>

		<div class="feasibility-query__output">
			<div class="output-header">
				<label>Ausgewählte Merkmale</label>
			</div>
			<div class="output-content">
				<div class="output-textfield" />
				<div class="pipe" />
				<div class="output-textfield" />
			</div>
		</div>
	</div>
</template>

<script>
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import SearchTreeOverlayContent from './SearchTreeOverlayContent.vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

export default {
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		SearchTreeOverlayContent,
	},
	data() {
		return {
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isEinschlusskriterienOverlayOpen: false,
			isAusschlusskriterienOverlayOpen: false,
			isAusschlusskriterien: true,
			mockupArr: ['Test1', 'Test2', 'Test3'],
			responseArray: [],
			filteredArr: [],
			ontologyResponseArr: [],
		}
	},
	computed: {},
	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getCsv()
		this.getOntology()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
		// Update when search
		const buttonContainer = document.getElementsByClassName('input-field__main-wrapper')
		buttonContainer[1].querySelector('input').addEventListener('focus', this.searchCodeEinschlusskriterien, true)
		buttonContainer[2].querySelector('input').addEventListener('focus', this.searchCodeAusschlusskriterien, true)
		if (buttonContainer[1].querySelector('button') !== null) {
			buttonContainer[1].querySelector('button').addEventListener('click', this.searchCodeEinschlusskriterien)
		}
		if (buttonContainer[2].querySelector('button') !== null) {
			buttonContainer[2].querySelector('button').addEventListener('click', this.searchCodeAusschlusskriterien)
		}
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getCsv() {
			const objData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/metadata'))
			const obj = objData.data
			this.responseArray = Object.values(obj)

			this.responseArray = this.responseArray
				.filter(
					(item) =>
						item[
							'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
						],
				)
				.filter((attr) => attr !== '' && attr !== undefined)

			this.filteredArr = this.responseArray
			// get modul name
			// this.modulName = this.getModulName(this.responseArray)
			// initialize keys from modulName.length (default: expand all attributelists)
			// this.expandedGroup = [...Array(this.modulName.length).keys()]
		},

		async getOntology() {
			const jsonData = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ontology'))
			const jsonObj = jsonData.data
			return jsonObj
		},

		switchSearchEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = !this.isEinschlusskriterienOverlayOpen
			this.isAusschlusskriterienOverlayOpen = false
			this.filteredSearch(this.einschlussTextSerach)
		},
		switchSearchAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = !this.isAusschlusskriterienOverlayOpen
			this.isEinschlusskriterienOverlayOpen = false
			this.filteredSearch(this.ausschlussTextSerach)
		},
		searchCodeEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = true
			this.isAusschlusskriterienOverlayOpen = false
			this.filteredSearch(this.einschlussTextSerach)
		},
		searchCodeAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = true
			this.isEinschlusskriterienOverlayOpen = false
			this.filteredSearch(this.ausschlussTextSerach)
		},
		filteredSearch(textSearch) {
			this.filteredArr = this.responseArray.filter((item) =>
				item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'].toLowerCase().includes(textSearch.toLowerCase()),
			)
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

.output-header label {
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
