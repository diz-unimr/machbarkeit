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
					@trailing-button-click="txtSearch = ''">
					<Magnify :size="20" />
				</NcTextField>
				<div class="attribute-display">
					<div v-for="(modul, index) in modulName" :key="index" class="attribute-display__header">
						<a class="modul-name" @click="toggleExpansion(index)">
							<img class="expandImg"
								:src="isExpanded(index)
									? imgExpand
									: imgCollapse
								">
							{{ modul }}
						</a>
						<div v-show="isExpanded(index)" style="margin-top: 10px">
							<!-- eslint-disable -->
							<div
								class="attribute-items"
								v-for="(item, key) in filteredAttribute"
								:key="key"
								v-if="
									item[
										'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
									] === modul
								"
							>
								<!-- eslint-enable -->
								<input :id="String(key)"
									v-model="checkedAttribute"
									type="checkbox"
									:value="item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name']"
									@change="selectAttribute">
								<!-- The for attribute is used in HTML to associate a <label> element with a form element -->
								<p :for="key"
									@mouseover="getTooltipPosition">
									{{ item['Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'] }}
								</p>
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
					<div v-for="(modul, index) in selectedModulName" :key="index">
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

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'

interface AttributeListData {
    attributeList: Array<object>,
    attributeName: Array<string>,
    modulName: Array<string>,
    expandedGroup: Array<number>,
    txtSearch: string,
    checkedAttribute: Array<string>,
    selectedAttribute: Array<object>,
    selectedModulName: Array<string>,
    tooltipPosition: number,
    imgExpand: string,
    imgCollapse:string,
}

export default Vue.extend({
	name: 'AttributeList',
	components: {
		NcTextField,
		Magnify,
	},
	data(): AttributeListData {
		return {
			attributeList: [],
			attributeName: [],
			modulName: [],
			expandedGroup: [],
			txtSearch: '',
			checkedAttribute: [],
			selectedAttribute: [],
			selectedModulName: [],
			tooltipPosition: 0,
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
		}
	},

	computed: {
		filteredAttribute(): Array<object> {
			return this.attributeList.filter((item) =>
				item[
					'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
				]
					.toLowerCase()
					.includes(this.txtSearch.toLowerCase()),
			)
		},
	},

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
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getCsv(): Promise<void> {
			const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/metadata'))
			this.attributeList = response.data
			this.modulName = this.getModuleName(this.attributeList)
			// initialize keys from modulName.length (default: expand all attributelists)
			this.expandedGroup = [...Array(this.modulName.length).keys()]
			this.attributeName = this.getAttributeName(this.attributeList)
		},

		getModuleName(attributeList: Array<object>): Array<string> {
			const modulName: Array<string> = attributeList
				.map(
					(item) =>
						item[
							'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
						],
				).filter(
					(item, index, array) => array.indexOf(item) === index && item !== undefined && item !== '',
				)
			return modulName
		},

		getAttributeName(attributeList: Array<object>): Array<string> {
			const attributeName = attributeList
				.map(
					(item) =>
						item[
							'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
						],
				).filter((attr) => attr !== '' && attr !== undefined)

			return attributeName
		},

		isExpanded(key: number): boolean {
			return this.expandedGroup.indexOf(key) !== -1
		},

		toggleExpansion(key: number): void {
			if (this.isExpanded(key)) {
				// .splice(start, deleteCount, item1, ..., itemN)
				this.expandedGroup.splice(this.expandedGroup.indexOf(key), 1)
			} else this.expandedGroup.push(key)
		},

		selectAttribute(): void {
			this.selectedAttribute = this.attributeList.filter((filteredItem) =>
				this.checkedAttribute.includes(
					filteredItem[
						'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
					],
				),
			)
			this.selectedModulName = this.getModuleName(this.selectedAttribute)
		},

		getTooltipPosition(event: MouseEvent): void {
			const targetElement = event.target as HTMLElement
			const labelPosition = targetElement.getBoundingClientRect()
			this.tooltipPosition = labelPosition.top - 136
			const tooltips = document.getElementsByClassName('attribute-tooltip') as HTMLCollectionOf<HTMLElement>
			for (let i = 0; i < tooltips.length; i++) {
				tooltips[i].style.top = this.tooltipPosition + 'px'
			}
		},
	},
})
</script>

<style scoped>
.attribute-list-container {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	row-gap: 20px;
	width: 460px;
	height: 100%;
	padding: 20px 0px 0px 20px;
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

.attribute-display__header {
	margin: 10px 0px;
}

.modul-name {
	font-weight: bold;
	margin-bottom: 2px;
}

.modul-name img {
	margin-right: 5px;
}

.attribute-items {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	min-height: 30px;
	padding-left: 20px;
}

.attribute-items input {
	position: relative;
	margin: 0px;
	top: -6px;
	width: 14px;
	height: 14px;
}

.attribute-items p {
	position: relative;
	display: inline-block;
}

.attribute-list__content input:focus {
	border: 2px solid #5a78ae;
}

.expandImg {
	width: 14px;
	height: 14px
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

.attribute-items p:hover + .attribute-tooltip {
	visibility: visible;
}
</style>
