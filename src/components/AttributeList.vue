<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="attribute-list-container">
		<div class="attribute-list">
			<div class="attribute-list__header">
				Attributliste
			</div>
			<div class="attribute-list__content">
				<NcTextField :value.sync="txtSearch"
					label="Attribut suchen"
					trailing-button-icon="close"
					placeholder=" "
					:show-trailing-button="txtSearch !== ''"
					@trailing-button-click="txtSearch = ''"
					@input="searchAttribute">
					<Magnify :size="20" />
				</NcTextField>
				<!-- <input class="attribute-search-input"
					v-model="txtSearch"
					type="text"
					placeholder="Attribut suchen"
					@input="searchAttribute"> -->
				<div class="attribute-display">
					<div v-for="(modul, index) in modulName" :key="index">
						<a class="modul-name" @click="toggleExpansion(index)">
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
								class="attribute-items"
								v-for="(item, key) in filteredArr"
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
									@mouseover="tooltipPosition">
									{{ item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'] }}
								</label>
								<span class="attribute-tooltip">{{ item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_description'] }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="attribute-list">
			<div class="attribute-list__header">
				ausgewählte Attributliste
			</div>
			<div class="attribute-list__content">
				<div class="attribute-display">
					<div v-for="(modul, index) in selectedArrModulName" :key="index">
						<div class="modul-name">
							{{ modul }}
						</div>
						<!-- eslint-disable -->
						<div
							class="attribute-items"
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
// import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'

export default {
	name: 'AttributeList',
	components: {
		NcTextField,
		Magnify,
	},
	data() {
		return {
			responseArray: [],
			filteredArr: [],
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
			this.modulName = this.getModulName(this.responseArray)
			// initialize keys from modulName.length (default: expand all attributelists)
			this.expandedGroup = [...Array(this.modulName.length).keys()]
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
			this.filteredArr = this.responseArray.filter((item) =>
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
			const tooltip = document.getElementsByClassName('attribute-tooltip')
			for (let i = 0; i < tooltip.length; i++) {
				tooltip[i].style.top = this.tooltip_Position + 'px'
			}
		},
	},
}
</script>

<style scoped>
.attribute-list-container {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	row-gap: 20px;
	width: 27%;
	height: 100%;
	padding: 20px 20px 0px 20px;
}

.attribute-list {
	position: relative;
	min-height: 45%;
	width: 100%;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.attribute-list__header {
	background-color: #5270a7;
	font-weight: bold;
	font-size: large;
	text-align: center;
	color: white;
	padding: 15px 0px;
}

.attribute-list__content {
	display: flex;
	flex-direction: column;
	position: absolute;
	width: 100%;
	height: -webkit-fill-available;
	padding: 20px;
}

.input-field {
	margin-block-start: 0px;
	margin-bottom: 15px;
}

.attribute-display {
	overflow-y: auto;
	overflow-x: hidden;
}

.modul-name {
	font-weight: bold;
	margin-bottom: 2px;
}

.attribute-items {
	display: flex;
	flex-direction: row;
	column-gap: 8px;
	min-height: 30px;
	padding-left: 15px;
}

.attribute-items input {
	margin: 0px;
	position: relative;
	top: -6px;
}

.attribute-items label {
	position: relative;
	display: inline-block;
}

.attribute-list__content input:focus {
	border: 2px solid #5a78ae;
}

.expandImg {
	width: 10px;
	height: 10px
}

.attribute-tooltip {
	display: flex;
	visibility: hidden;
	width: 350px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
	padding: 10px;
	position: absolute;
	z-index: 1000;
	left: 105%;
}

.attribute-tooltip::after {
	content: '';
	position: absolute;
	top: 35%;
	left: -8%; /* -11% */
	border-width: 8px;
	border-style: solid;
	border-color: transparent #5270a7 transparent transparent;
}

.attribute-items label:hover + .attribute-tooltip {
	visibility: visible;
}

</style>
