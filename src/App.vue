<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<!-- eslint no-trailing-spaces: "error" -->
	<div id="content" class="app-machbarkeit">
		<div id="container">
			<div id="content-left">
				<div id="attribute-list-container">
					<div id="attribute-list-header">Attributliste</div>
					<div id="attribute-list-content">
						<input
							id="search-input"
							v-model="txtSearch"
							type="text"
							placeholder="Attribut suchen"
							@input="searchAttribute"
						/>
						<div style="overflow: auto">
							<div v-for="(modul, index) in modulName" :key="index">
								<a id="modul-name" @click="toggleExpansion(index)">
									{{ modul }}
									<img
										style="width: 10px; height: 10px"
										:src="
											isExpanded(index)
												? 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png'
												: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png'
										"
									/>
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
										<input
											:id="key"
											v-model="selectedArr"
											type="checkbox"
											:value="item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name']"
											@change="showSelectedAttribute"
										/>
										<!-- The for attribute is used in HTML to associate a <label> element with a form element -->
										<label :for="key">{{ item[ 'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name']
										}}</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="attribute-list-container">
					<div id="attribute-list-header">ausgewählte Attributliste</div>

					<div id="attribute-list-content">
						<div style="overflow: auto; width: 100%">
							<div v-for="(modul, index) in selectedArrModulName" :key="index">
								<div id="modul-name">{{ modul }}</div>
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
			<div id="content-right">Right Content</div>
		</div>
	</div>
</template>

<script>
import '@nextcloud/dialogs/styles/toast.scss'
import Papa from 'papaparse'

export default {
	name: 'App',
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
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.loadcsv()
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		loadcsv() {
			const response = fetch(
				'http://localhost:8080/apps-extra/machbarkeit/csvfile/diz.csv',
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
	},
}
</script>
<style scoped>
#container {
	display: flex;
	flex-wrap: wrap;
	width: 100%;
	background-color: white;
}

#content-left {
	width: 22%;
	height: 90%;
	margin: 20px;
}

#attribute-list,
#content-left #selected-attribute {
	display: flex;
	flex-direction: column;
	height: 50%;
	background-color: white;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	padding: 20px;
	margin-bottom: 20px;
}

#attribute-list-container {
	height: 50%;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	margin-bottom: 25px;
}

#attribute-list-header {
	background-color: #5270a7;
	font-weight: bold;
	font-size: large;
	text-align: center;
	color: white;
	padding: 15px 0px;
}

#attribute-list-content {
	display: flex;
	flex-direction: column;
	padding: 20px;
	height: 90%;
}

#content-right {
	margin: 10px;
	width: 70%;
	height: 1000px;
}

#app-content > div {
	width: 100%;
	height: 100%;
	padding: 20px;
	display: flex;
	flex-direction: column;
	flex-grow: 1;
}

#toggle-img {
	width: 15px;
	height: 15px;
}

input[type='text'] {
	width: 100%;
}

textarea {
	flex-grow: 1;
	width: 100%;
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

#search-input {
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 512 512' fill='gray'%3E%3Cpath d='M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position-y: center;
	background-position-x: 10px;
	width: 100%;
	margin-bottom: 20px;
	border: 1px solid #c0c7ce;
	padding-left: 35px;
}
</style>
