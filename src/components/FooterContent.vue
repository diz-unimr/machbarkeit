<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="footer-container">
		<div class="footer__button-group">
			<input id="upload"
				ref="fileInput"
				type="file"
				accept="application/json"
				hidden
				@change="handleFileUpload">
			<label for="upload" class="upload-query-button">
				ABFRAGE LADEN
			</label>
			<button :disabled="!isCriteriaAvailable" @click="$emit('open-save-modal')">
				ABFRAGE SPEICHERN
			</button>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'
import transformObjectKeys from '../utils/transformObjectKeys'
import type { QueryCriterionData, FeasibilityQueryBuilderData, SelectedCharacteristics } from '../types/FeasibilityQueryBuilderData'
import type { Criterion, Modules } from '../types/OntologySearchTreeModalData.ts'
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRangeType } from '../types/TimeRangeOptionsData'

export default Vue.extend({
	name: 'FooterContent',
	props: {
		isCriteriaAvailable: {
			type: Boolean,
			default: false,
		},
	},

	methods: {
		handleFileUpload(event: Event) {
			const target = event.target as HTMLInputElement
			if (target.files && target.files.length > 0) {
				const file = target.files[0]
				const reader = new FileReader()
				reader.onload = async (e) => {
					try {
						const uploadedCriteria: FeasibilityQueryBuilderData['queryData'] = JSON.parse(e.target?.result as string)
						const isJsonDataValid = (uploadedCriteria.inclusionCriteria && uploadedCriteria.inclusionCriteria!.length > 0) || (uploadedCriteria.exclusionCriteria && uploadedCriteria.exclusionCriteria!.length > 0)
						if (isJsonDataValid) {
							await this.getCriteriaInfo(uploadedCriteria)
							this.$emit('get-query-data', uploadedCriteria)
						} else {
							alert('Invalid JSON Format')
						}
					} catch (error) {
						alert('Invalid JSON Format')
					}
				}
				reader.readAsText(file)
			}
			target.value = ''
		},

		async getCriteriaInfo(criteria: FeasibilityQueryBuilderData['queryData']) {
			const inclusionCharacteristics : SelectedCharacteristics = {
				characteristics: [],
				logic: [],
			}
			const exclusionCharacteristics: SelectedCharacteristics = {
				characteristics: [],
				logic: [],
			}

			const response = (await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/modules'))
			const modules: Modules[] = transformObjectKeys(response.data)

			if (criteria.inclusionCriteria) {
				let tempIndex = 0
				for (let itemsIndex = 0; itemsIndex < criteria.inclusionCriteria.length; itemsIndex++) {
					itemsIndex !== 0 && inclusionCharacteristics.logic.push('and')
					for (let itemIndex = 0; itemIndex < criteria.inclusionCriteria[itemsIndex].length; itemIndex++) {
						const id = criteria.inclusionCriteria[itemsIndex][itemIndex].id
						const response = (await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/concepts/' + id))
						const ontology: Criterion = transformObjectKeys([response.data])[0]

						const module = modules.find((module) => module.id === ontology.moduleId)
						ontology.context = {
							code: module!.fdpgCdsCode,
							display: module!.name,
							system: module!.fdpgCdsSystem,
							version: module!.fdpgCdsVersion,
						}

						inclusionCharacteristics.characteristics.push(ontology)

						if (['valueFilter', 'timeRestriction'].some(key => key in criteria.inclusionCriteria![itemsIndex][itemIndex])) {
							switch (ontology.filterType) {
							case 'concept': {
								const item = criteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & ConceptType
								inclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
									valueFilter: item.valueFilter,
								}
								break
							}
							case 'quantity': {
								const item = criteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & QuantityType
								inclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
									valueFilter: item.valueFilter,
								}
								break
							}
							default: {
								if (ontology.timeRestrictionAllowed) {
									const item = criteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & TimeRangeType
									inclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
										timeRestriction: item.timeRestriction,
									}
								}
							}
							}
						}
						itemIndex !== 0 && inclusionCharacteristics.logic.push('or')
						tempIndex++
					}
				}
			}
			if (criteria.exclusionCriteria) {
				let tempIndex = 0
				for (let itemsIndex = 0; itemsIndex < criteria.exclusionCriteria.length; itemsIndex++) {
					itemsIndex !== 0 && exclusionCharacteristics.logic.push('or')
					for (let itemIndex = 0; itemIndex < criteria.exclusionCriteria[itemsIndex].length; itemIndex++) {
						const id = criteria.exclusionCriteria[itemsIndex][itemIndex].id
						const response = (await axios.get('https://mdr.diz.uni-marburg.de/api/ontology/concepts/' + id))
						const ontology: Criterion = transformObjectKeys([response.data])[0]

						const module = modules.find((module) => module.id === ontology.moduleId)
						ontology.context = {
							code: module!.fdpgCdsCode,
							display: module!.name,
							system: module!.fdpgCdsSystem,
							version: module!.fdpgCdsVersion,
						}

						exclusionCharacteristics.characteristics.push(ontology)

						if (['valueFilter', 'timeRestriction'].some(key => key in criteria.exclusionCriteria![itemsIndex][itemIndex])) {
							switch (ontology.filterType) {
							case 'concept': {
								const item = criteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & ConceptType
								exclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
									valueFilter: item.valueFilter,
								}
								break
							}
							case 'quantity': {
								const item = criteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & QuantityType
								exclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
									valueFilter: item.valueFilter,
								}
								break
							}
							default: {
								if (ontology.timeRestrictionAllowed) {
									const item = criteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterionData & TimeRangeType
									exclusionCharacteristics.characteristics[tempIndex].selectedFilter = {
										timeRestriction: item.timeRestriction,
									}
								}
							}
							}
						}
						itemIndex !== 0 && exclusionCharacteristics.logic.push('and')
						tempIndex++
					}
				}
			}
			this.$emit('send-criteria-to-display', { inclusionCharacteristics, exclusionCharacteristics })
		},
	},
})
</script>

<style scoped>
.footer-container {
	display: flex;
	justify-content: flex-end;
	align-items: flex-end;
	margin-top: 20px;
	width: 100%;
}

.footer__button-group {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

.upload-query-button {
	border-radius: 8px;
	font-weight: bold;
	padding: 8px 14px;
	margin: 3px 3px 3px 0;
	width: auto;
	min-height: 36px;
	cursor: pointer;
	box-sizing: border-box;
	background-color: #ededed;
	border: 1px solid #dbdbdb;
	color: #222222;
	outline: none;
}

.upload-query-button:hover {
	border: 1px solid #006aa3;
}

.upload-query-button:active {
	background-color: white;
}

button {
	border-radius: 8px;
}
</style>
