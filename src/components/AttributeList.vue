<template>
	<div id="attribute-list">
		<div id="attribute-list__container">
			<div id="attribute-list__header">
				Attributliste
			</div>
			<div id="attribute-list__content" ref="attribute-list__content">
				<!-- v-model="txtSearch" -->
				<NcTextField :value.sync="txtSearch"
					label="Attribut suchen"
					trailing-button-icon="close"
					placeholder=" "
					:show-trailing-button="txtSearch !== ''"
					@trailing-button-click="txtSearch = ''"
					@input="searchAttribute">
					<Magnify :size="20" />
				</NcTextField>
				<!-- <input id="attribute-search-input"
					v-model="txtSearch"
					type="text"
					placeholder="Attribut suchen"
					@input="searchAttribute"> -->
				<div class="attribute-list-modul">
					<div v-for="(modul, index) in modulName" :key="index">
						<a id="modul-name" @click="toggleExpansion(index)">
							{{ modul }}
							<img class="expandImg"
								:src="isExpanded(index)
									? 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png'
									: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png'
								">
						</a>
						<div v-show="isExpanded(index)" style="margin-top: 10px">
							<!-- eslint-disable -->
							<div
								id="attribute-items"
								v-for="(item, key) in fillteredArr"
								:key="key"
								v-if="
									item[
										'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
									] === modul
								"
							>
								<!-- eslint-enable -->
								<input :id="key"
									v-model="selectedArr"
									type="checkbox"
									:value="item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name']"
									@change="showSelectedAttribute">
								<!-- The for attribute is used in HTML to associate a <label> element with a form element -->
								<label :for="key"
									class="attribute-item__tooltip"
									@mouseover="tooltipPosition">
									{{ item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'] }}
								</label>
								<span class="tooltip-text">{{ item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_description'] }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="attribute-list__container">
			<div id="attribute-list__header">
				ausgewählte Attributliste
			</div>
			<div id="attribute-list__content">
				<div class="attribute-list-modul">
					{{ data.data }}
					<div v-for="(modul, index) in selectedArrModulName" :key="index">
						<div id="modul-name">
							{{ modul }}
						</div>
						<!-- eslint-disable -->
						<div
							id="attribute-items"
							v-for="item in selectedAttribute"
							v-if="
								item[
									'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
								] === modul
							"
						>
							<!-- eslint-enable -->
							<label style="cursor: auto">{{
								item[
									"Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
								]
							}}</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import '@nextcloud/dialogs/styles/toast.scss'
import Papa from 'papaparse'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify'

export default {
	name: 'AttributeList',
	components: {
		NcTextField,
		Magnify,
	},
	data() {
		return {
			responseArray: [],
			fillteredArr: [],
			selectedArr: [],
			selectedAttribute: [],
			expandedGroup: [],
			modulName: [],
			selectedArrModulName: [],
			txtSearch: '',
			tooltip_Position: 0,
			jsonData: [],
			data: [],
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.loadcsv()
		this.getCsv()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
		const buttonContainer = document.getElementsByClassName('input-field__main-wrapper')
		if (buttonContainer[0].querySelector('button') !== null) {
			buttonContainer[0].querySelector('button').addEventListener('click', this.searchAttribute)
		}
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getCsv() {
			this.data = await axios.get(generateUrl('/apps/machbarkeit/api/0.1/machbarkeit/metadata'))
		},

		loadcsv() {
			const response = fetch(
				'http://localhost:8080/apps-extra/machbarkeit/csvfile/diz_metadaten.csv',
			)
				.then((res) => res.text())
				.then((text) => Papa.parse(text, { header: true }))
				.catch((e) => console.error(e))
			response.then((v) => {
				// get all data from csv file
				this.responseArray = Object.values(v.data)
				this.responseArray = this.responseArray
					.filter(
						(item) =>
							item[
								'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
							],
					)
					.filter((attr) => attr !== '' && attr !== undefined)

				this.fillteredArr = this.responseArray
				// get modul name
				this.modulName = this.getModulName(this.responseArray)
				// initialize keys from modulName.length (default: expand all attributelists)
				this.expandedGroup = [...Array(this.modulName.length).keys()]
			})
			return response
		},

		isExpanded(key) {
			return this.expandedGroup.indexOf(key) !== -1
		},

		toggleExpansion(key) {
			if (this.isExpanded(key)) {
				// .splice(start, deleteCount, item1, ..., itemN)
				this.expandedGroup.splice(this.expandedGroup.indexOf(key), 1)
			} else this.expandedGroup.push(key)
		},

		searchAttribute() {
			// delete empty string and undefined from attribute name
			this.responseArray = this.responseArray
				.filter(
					(item) =>
						item[
							'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
						],
				)
				.filter((attr) => attr !== '' && attr !== undefined)

			// filter txtSearch
			this.fillteredArr = this.responseArray.filter((item) =>
				item[
					'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
				]
					.toLowerCase()
					.includes(this.txtSearch.toLowerCase()),
			)
		},

		getModulName(inputArr) {
			let name
			name = inputArr.map(
				(item) =>
					item[
						'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
					],
			)
			// filter duplicate
			name = name.filter(
				(item, index) =>
					name.indexOf(item) === index && item !== undefined && item !== '',
			)
			return name
		},

		showSelectedAttribute() {
			this.selectedAttribute = this.responseArray.filter((filteredItem) =>
				this.selectedArr.includes(
					filteredItem[
						'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
					],
				),
			)
			this.selectedArrModulName = this.getModulName(this.selectedAttribute)
		},

		tooltipPosition(event) {
			const labelPosition = event.target.getBoundingClientRect()
			this.tooltip_Position = labelPosition.top - 136
			const tooltip = document.getElementsByClassName('tooltip-text')
			for (let i = 0; i < tooltip.length; i++) {
				tooltip[i].style.top = this.tooltip_Position + 'px'
			}
		},
	},
}
</script>

<style scoped>
#attribute-list {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	row-gap: 20px;
	width: 27%;
	height: 100%;
	padding: 20px 20px 0px 20px;
}

#attribute-list #selected-attribute {
	display: flex;
	flex-direction: column;
	height: 50%;
	background-color: white;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	padding: 20px;
	margin-bottom: 20px;
}

#attribute-list__container {
	position: relative;
	min-height: 45%;
	width: 100%;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

#attribute-list__header {
	background-color: #5270a7;
	font-weight: bold;
	font-size: large;
	text-align: center;
	color: white;
	padding: 15px 0px;
}

#attribute-list__content {
	position: absolute;
	display: flex;
	flex-direction: column;
	padding: 20px;
	height: -webkit-fill-available;
	width: 100%;
}

.attribute-list-modul {
	overflow-y: auto;
	overflow-x: hidden;
}

#attribute-items {
	display: flex;
	flex-direction: row;
	column-gap: 8px;
	min-height: 30px;
	padding-left: 15px;
}

#attribute-items input {
	margin: 0px;
	position: relative;
	top: -6px;
}

#modul-name {
	font-weight: bold;
	margin-bottom: 2px;
}

#attribute-search-input {
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 512 512' fill='gray'%3E%3Cpath d='M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position-y: center;
	background-position-x: 10px;
	width: 100%;
	margin-bottom: 20px;
	border: 1px solid #c0c7ce;
	padding-left: 35px;
}

#attribute-list__content input:focus {
	border: 2px solid #5a78ae;
}

.expandImg {
	width: 10px;
	height: 10px
}

.attribute-item__tooltip {
	position: relative;
	display: inline-block;
}

.tooltip-text {
	display: flex;
	visibility: hidden;
	width: 350px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
	padding: 10px;
	position: absolute;
	z-index: 1;
	left: 105%;
}

.tooltip-text::after {
	content: '';
	position: absolute;
	top: 35%;
	left: -8%; /* -11% */
	border-width: 8px;
	border-style: solid;
	border-color: transparent #5270a7 transparent transparent;
}

.attribute-item__tooltip:hover .tooltip-text {
	visibility: visible;
}

.attribute-item__tooltip:hover + .tooltip-text {
	visibility: visible;
}

#content-right {
	width: 100%;
	height: 100%;
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: flex-start center;
	align-items: flex-start;
}

#feasibility-query {
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: flex-start center;
	align-items: flex-start;
	flex: 1 1 100%;
	max-width: 90%;
}

.input-field {
	margin-block-start: 0px;
	margin-bottom: 15px;
}

.show-tooltip {
	position: relative;
	display: inline-block;
}

.show-tooltip .tooltiptext {
	display: flex;
	visibility: hidden;
	width: 300px;
	height: auto;
	background-color: white;
	padding: 10px;
	position: absolute;
	z-index: 100;
	left: 150%;
	top: -160%;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.show-tooltip:hover .tooltiptext {
	visibility: visible;
}
</style>
