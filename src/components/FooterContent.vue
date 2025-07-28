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
			<button :disabled="!hasSelectedCharacteristic" @click="$emit('open-save-modal')">
				ABFRAGE SPEICHERN
			</button>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { AxiosError } from 'axios'
import { getModules } from '../services/modules-service'
import { getConcept } from '../services/ontology-service'
import type { CharacteristicGroup, MachbarkeitQueryData, QueryCriterion } from '../types/FeasibilityQueryContainerData'
import type { Module } from '../types/OntologyPanelData'
import type { ConceptType } from '../types/ConceptOptionsData'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRangeType } from '../types/TimeRangeOptionsData'

export default Vue.extend({
	name: 'FooterContent',
	computed: {
		hasSelectedCharacteristic(): boolean {
			return this.$store.state.selectedCharacteristics.inclusionCriteria.characteristics.length > 0 || this.$store.state.selectedCharacteristics.exclusionCriteria.characteristics.length > 0
		},
	},
	methods: {
		handleFileUpload(event: Event) {
			const target = event.target as HTMLInputElement
			if (target.files && target.files.length > 0) {
				const file = target.files[0]
				const reader = new FileReader()
				reader.onload = (e) => {
					try {
						const uploadedCriteria: MachbarkeitQueryData = JSON.parse(e.target?.result as string)
						const isJsonDataValid = !!(uploadedCriteria.inclusionCriteria && uploadedCriteria.inclusionCriteria!.length > 0) || (uploadedCriteria.exclusionCriteria && uploadedCriteria.exclusionCriteria!.length > 0)
						if (isJsonDataValid) {
							this.convertToCharacteristicsDisplay(uploadedCriteria)
						} else {
							alert('Invalid JSON Format')
						}
					} catch (error) {
						alert((error as AxiosError).message)
					}
				}
				reader.readAsText(file)
			}
			target.value = ''
		},

		async convertToCharacteristicsDisplay(uploadedCriteria: MachbarkeitQueryData) {
			const inclusionCriteria : CharacteristicGroup = {
				characteristics: [],
				logic: [],
			}
			const exclusionCriteria: CharacteristicGroup = {
				characteristics: [],
				logic: [],
			}
			const modules = await getModules()

			if (uploadedCriteria.inclusionCriteria && modules) {
				let tempIndex = 0
				for (let itemsIndex = 0; itemsIndex < uploadedCriteria.inclusionCriteria.length; itemsIndex++) {
					itemsIndex !== 0 && inclusionCriteria.logic.push('and')
					for (let itemIndex = 0; itemIndex < uploadedCriteria.inclusionCriteria[itemsIndex].length; itemIndex++) {
						const id = uploadedCriteria.inclusionCriteria[itemsIndex][itemIndex].id
						const concept = await getConcept(id)
						if (!concept) continue

						const module: Module | undefined = modules.find((module) => module.id === concept.moduleId)
						if (module) {
							concept.color = module.color
							concept.context = {
								code: module.fdpgCdsCode,
								display: module.name,
								system: module.fdpgCdsSystem,
								version: module.fdpgCdsVersion,
							}
						}
						inclusionCriteria.characteristics.push(concept)

						if (['valueFilter', 'timeRestriction'].some(key => key in uploadedCriteria.inclusionCriteria![itemsIndex][itemIndex])) {
							switch (concept.filterType) {
							case 'concept': {
								const item = uploadedCriteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & ConceptType
								inclusionCriteria.characteristics[tempIndex].valueFilter = item.valueFilter
								break
							}
							case 'quantity': {
								const item = uploadedCriteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & QuantityType
								inclusionCriteria.characteristics[tempIndex].valueFilter = item.valueFilter
								break
							}
							default: {
								if (concept.timeRestrictionAllowed) {
									const item = uploadedCriteria.inclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & TimeRangeType
									inclusionCriteria.characteristics[tempIndex].timeRestriction = item.timeRestriction
								}
							}
							}
						}
						itemIndex !== 0 && inclusionCriteria.logic.push('or')
						tempIndex++
					}
				}
			}
			if (uploadedCriteria.exclusionCriteria && modules) {
				let tempIndex = 0
				for (let itemsIndex = 0; itemsIndex < uploadedCriteria.exclusionCriteria.length; itemsIndex++) {
					itemsIndex !== 0 && exclusionCriteria.logic.push('or')
					for (let itemIndex = 0; itemIndex < uploadedCriteria.exclusionCriteria[itemsIndex].length; itemIndex++) {
						const id = uploadedCriteria.exclusionCriteria[itemsIndex][itemIndex].id
						const concept = await getConcept(id)
						if (!concept) continue

						const module: Module | undefined = modules.find((module) => module.id === concept.moduleId)
						if (module) {
							concept.color = module.color
							concept.context = {
								code: module.fdpgCdsCode,
								display: module.name,
								system: module.fdpgCdsSystem,
								version: module.fdpgCdsVersion,
							}
						}
						exclusionCriteria.characteristics.push(concept)

						if (['valueFilter', 'timeRestriction'].some(key => key in uploadedCriteria.exclusionCriteria![itemsIndex][itemIndex])) {
							switch (concept.filterType) {
							case 'concept': {
								const item = uploadedCriteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & ConceptType
								exclusionCriteria.characteristics[tempIndex].valueFilter = item.valueFilter
								break
							}
							case 'quantity': {
								const item = uploadedCriteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & QuantityType
								exclusionCriteria.characteristics[tempIndex].valueFilter = item.valueFilter
								break
							}
							default: {
								if (concept.timeRestrictionAllowed) {
									const item = uploadedCriteria.exclusionCriteria[itemsIndex][itemIndex] as QueryCriterion & TimeRangeType
									exclusionCriteria.characteristics[tempIndex].timeRestriction = item.timeRestriction
								}
							}
							}
						}
						itemIndex !== 0 && exclusionCriteria.logic.push('and')
						tempIndex++
					}
				}
			}
			this.$emit('get-data-from-upload', {
				uploadedCriteria,
				characteristicsDisplay: {
					inclusionCriteria,
					exclusionCriteria,
				},
			})
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
	background-color: var(--color-primary-element-light);
	color: #222222;
	outline: none;
}

.upload-query-button:hover {
	background-color: var(--color-primary-element-light-hover);
}

.upload-query-button:active {
	background-color: white;
}

button {
	border-radius: 8px;
}
</style>
