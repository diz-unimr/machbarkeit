<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="attribute-list-container">
		<div class="attribute-list-wrapper">
			<div class="attribute-list__header">
				Attributliste
			</div>
			<div class="attribute-list__content">
				<NcTextField :value.sync="attributeTextSearch"
					label="Attribut suchen"
					trailing-button-icon="close"
					placeholder=" "
					:show-trailing-button="attributeTextSearch !== ''"
					@trailing-button-click="attributeTextSearch = ''">
					<Magnify :size="20" />
				</NcTextField>
				<div class="attribute-list__display">
					<div v-for="(module, index) in moduleName" :key="index">
						<a class="module-name" @click="toggleExpansion(index)">
							<img class="module-expand-img"
								:src="isExpanded(index)
									? imgExpand
									: imgCollapse
								">
							{{ module }}
						</a>
						<div v-show="isExpanded(index)">
							<template v-for="(item, item_index) in filteredAttribute">
								<div v-if="item[metadata.kds_modul] === module"
									:key="item_index"
									class="attribute-name">
									<input :id="String(item_index)"
										v-model="selectedAttribute"
										type="checkbox"
										:value="item"
										@change="selectAttribute">
									<!-- The for attribute is used in HTML to associate a <label> element with a form element -->
									<p :for="item_index"
										@mouseover="getTooltipPosition">
										{{ item[metadata.kds_name] }}
									</p>
									<span class="attribute-tooltip-text">{{ item[metadata.kds_description] }}</span>
								</div>
							</template>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="attribute-list-wrapper">
			<div class="attribute-list__header">
				ausgewählte Attributliste
			</div>
			<div class="attribute-list__content">
				<div class="attribute-list__display">
					<div v-for="(module, index) in selectedModulName" :key="index">
						<div class="module-name module-name--selected-attribute">
							{{ module }}
						</div>
						<template v-for="(item, item_index) in selectedAttribute">
							<div v-if="item[metadata.kds_modul] === module"
								:key="item_index"
								class="attribute-name">
								<label style="cursor: auto">
									{{ item[metadata.kds_name] }}
								</label>
							</div>
						</template>
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
    attributeList: Array<object>;
    attributeName: Array<string>;
    moduleName: Array<string>;
    expandedGroup: Array<number>;
	attributeTextSearch: string;
    selectedAttribute: Array<object>;
    selectedModulName: Array<string>;
    tooltipPosition: number;
    imgExpand: string;
    imgCollapse:string;
	metadata: {
		kds_modul: string;
		kds_name: string;
		kds_description: string;
	},
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
			moduleName: [],
			expandedGroup: [],
			attributeTextSearch: '',
			selectedAttribute: [],
			selectedModulName: [],
			tooltipPosition: 0,
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			metadata: {
				kds_modul: 'Main.Metadatenrepository.KDS-Module.Code.KDS-ModuleClass_KDS_MODUL',
				kds_name: 'Main.Metadatenrepository.KDS-Module.Code.KDS-ModuleClass_ATTRIBUT_NAME',
				kds_description: 'Main.Metadatenrepository.KDS-Module.Code.KDS-ModuleClass_ATTRIBUT_DESCRIPTION',
			},
		}
	},

	computed: {
		filteredAttribute(): Array<object> {
			return this.attributeList.filter((item) =>
				item[this.metadata.kds_name].toLowerCase().includes(this.attributeTextSearch.toLowerCase()),
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
			this.attributeList = this.sortData(this.attributeList)
			this.moduleName = this.getModuleName(this.attributeList)
			// initialize keys from moduleName.length (default: expand all attributelists)
			this.expandedGroup = [...Array(this.moduleName.length).keys()]
			this.attributeName = this.getAttributeName(this.attributeList)
		},

		sortData(data: object[]): object[] {
			data.sort((a, b) => {
				const moduleNameComparison = a[this.metadata.kds_modul].localeCompare(b[this.metadata.kds_modul])
				if (moduleNameComparison !== 0) return moduleNameComparison

				const attributeNameComparison = a[this.metadata.kds_name].localeCompare(b[this.metadata.kds_name])
				if (attributeNameComparison !== 0) return attributeNameComparison

				return 0
			})
			return data
		},

		getModuleName(attributeList: Array<object>): Array<string> {
			const moduleName: Array<string> = attributeList
				.map(
					(item) => item[this.metadata.kds_modul],
				).filter(
					(item, index, array) => array.indexOf(item) === index && item !== undefined && item !== '',
				)
			return moduleName
		},

		getAttributeName(attributeList: Array<object>): Array<string> {
			const attributeName = attributeList
				.map(
					(item) => item[this.metadata.kds_name],
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
			this.selectedModulName = this.getModuleName(this.selectedAttribute)
		},

		getTooltipPosition(event: MouseEvent): void {
			const targetElement = event.target as HTMLElement
			const labelPosition = targetElement.getBoundingClientRect()
			this.tooltipPosition = labelPosition.top - 136
			const tooltips = document.getElementsByClassName('attribute-tooltip-text') as HTMLCollectionOf<HTMLElement>
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

.attribute-list-wrapper {
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

.attribute-list__content input:focus {
	border: 2px solid #5a78ae;
}

.input-field {
	margin-block-start: 0px;
	margin-bottom: 15px;
}

.attribute-list__display {
	display: flex;
	flex-direction: column;
	row-gap: 10px;
	overflow-y: auto;
	overflow-x: hidden;
}

.module-name {
	display: inline-block;
	font-weight: bold;
	margin-bottom: 10px;
}

.module-name--selected-attribute {
	margin-bottom: 5px;
}

.attribute-name {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	min-height: 30px;
	padding-left: 20px;
}

.attribute-name input {
	position: relative;
	margin: 0px;
	top: -6px;
	width: 14px;
	height: 14px;
}

.attribute-name p {
	position: relative;
	display: inline-block;
}

.module-expand-img {
	width: 14px;
	height: 14px;
	margin-right: 5px;
}

.attribute-tooltip-text {
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

.attribute-tooltip-text::after {
	content: '';
	position: absolute;
	top: 35%;
	left: -8%; /* -11% */
	border-width: 8px;
	border-style: solid;
	border-color: transparent #5270a7 transparent transparent;
}

.attribute-name p:hover + .attribute-tooltip-text {
	visibility: visible;
}

p {
	font-size: 15px;
}
</style>
