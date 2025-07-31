<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-container">
		<div class="feasibility-query-wrapper">
			<div class="feasibility-query__output">
				<div class="number-patients">
					<p>
						Anzahl der Patienten:
						<span v-if="isQueryRunning">
							<img src="http://localhost:8080/apps-extra/machbarkeit/img/loading_spinner.svg">
						</span>
						<template v-else-if="numberOfPatients !== null && hasSelectedCharacteristics">
							<span v-if="numberOfPatients <= 3" class="error-message">Das Ergebnis ist zu klein</span>
							<span v-else>{{ numberOfPatients }}</span>
						</template>
						<span v-else-if="errorMessage" class="error-message">{{ errorMessage }}</span>
						<span v-else>-</span>
					</p>
				</div>
				<div class="feasibility-query__button-group">
					<button :disabled="!hasSelectedCharacteristics" @click="resetSelectedCriteria()">
						ZURÜCKSETZEN
					</button>
					<button :disabled="!hasSelectedCharacteristics" @click="queryData && toggleQuery(queryData)">
						{{ isQueryRunning ? 'ABFRAGE STOPPEN': 'ABFRAGE STARTEN' }}
					</button>
				</div>
			</div>

			<SaveQueryModal v-if="isSaveModalOpen"
				:query-data="queryData"
				@close-save-modal="closeSaveModal" />

			<FeasibilityQueryBuilder ref="childComponent"
				:is-criteria-available="hasSelectedCharacteristics"
				:is-save-modal-open="isSaveModalOpen" />
		</div>
		<MachbarkeitFooter :is-criteria-available="hasSelectedCharacteristics"
			@open-save-modal="openSaveModal"
			@close-save-modal="closeSaveModal"
			@get-data-from-upload="getDataFromUpload" />
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import FeasibilityQueryBuilder from './FeasibilityQueryBuilder.vue'
import SaveQueryModal from './SaveQueryModal.vue'
import MachbarkeitFooter from './FooterContent.vue'
import type { FeasibilityQueryContainerData, MachbarkeitQueryData, SelectedCharacteristics, QueryCriterion } from '../types/FeasibilityQueryContainerData'
import { getMachbarkeit } from '../services/machbarkeit-service'

export default Vue.extend({
	name: 'FeasibilityQueryContainer',
	components: {
		FeasibilityQueryBuilder,
		SaveQueryModal,
		MachbarkeitFooter,
	},

	data(): FeasibilityQueryContainerData {
		return {
			queryData: null,
			numberOfPatients: null,
			isSaveModalOpen: false,
			errorMessage: null,
			isQueryRunning: false,
			abortController: null,
			hasDataFromUpload: false,
		}
	},

	computed: {
		selectedCharacteristics() {
			return this.$store.state.selectedCharacteristics
		},

		hasSelectedCharacteristics() {
			return this.selectedCharacteristics.inclusionCriteria.characteristics.length > 0 || this.selectedCharacteristics.exclusionCriteria.characteristics.length > 0
		},
	},

	watch: {
		// Get update selectedCharacteristics
		selectedCharacteristics: {
			handler(newValue) {
				if (!this.hasDataFromUpload) {
					this.clearRunningQuery()
					this.queryData = this.updateQueryData(newValue)
				}
				this.hasDataFromUpload = false
			},
			deep: true,
		},
	},

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
		openSaveModal(): void {
			this.isSaveModalOpen = true
		},

		closeSaveModal(): void {
			this.isSaveModalOpen = false
		},

		clearRunningQuery(): void {
			if (this.isQueryRunning) {
				this.isQueryRunning = false
				if (this.abortController) {
					this.abortController.abort()
					this.abortController = null
				}
			}
			this.numberOfPatients = null
			this.errorMessage = null
			this.isQueryRunning = false
		},

		resetSelectedCriteria() {
			this.clearRunningQuery()
			this.$store.dispatch('clearSelectedCharacteristics')
		},

		updateQueryData(data: SelectedCharacteristics): MachbarkeitQueryData {
			const templateQuery = {
				version: '1.0',
				display: 'Feasibility Query',
				inclusionCriteria: [] as QueryCriterion[][],
				exclusionCriteria: [] as QueryCriterion[][],
			}
			if (data.inclusionCriteria.characteristics.length > 0) {
				const inclusioncharacteristics = data.inclusionCriteria.characteristics
				const inclusionLogic = data.inclusionCriteria.logic || []

				let tempIndex = 0
				for (let i = 0; i < inclusioncharacteristics.length; i++) {
					const selectedCharacteristic = {
						id: inclusioncharacteristics[i].id,
						termCodes: inclusioncharacteristics[i].termCodes,
						context: inclusioncharacteristics[i].context,
						...(inclusioncharacteristics[i].valueFilter
							? { valueFilter: inclusioncharacteristics[i].valueFilter }
							: inclusioncharacteristics[i].timeRestriction
								? { timeRestriction: inclusioncharacteristics[i].timeRestriction }
								: {}
						),
					} as QueryCriterion
					if (i === 0) {
						templateQuery.inclusionCriteria.push([selectedCharacteristic])
					} else {
						if (inclusionLogic[i - 1] === 'or') {
							templateQuery.inclusionCriteria[tempIndex].push(selectedCharacteristic)
						} else if (inclusionLogic[i - 1] === 'and') {
							templateQuery.inclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						}
					}
				}
			} // test

			if (data.exclusionCriteria.characteristics.length > 0) {
				const exclusioncharacteristics = data.exclusionCriteria.characteristics
				const exclusionLogic = data.exclusionCriteria.logic || []

				let tempIndex = 0
				for (let i = 0; i < exclusioncharacteristics.length; i++) {
					const selectedCharacteristic = {
						id: exclusioncharacteristics[i].id,
						termCodes: exclusioncharacteristics[i].termCodes,
						context: exclusioncharacteristics[i].context,
						...(exclusioncharacteristics[i].valueFilter
							? { valueFilter: exclusioncharacteristics[i].valueFilter }
							: exclusioncharacteristics[i].timeRestriction
								? { timeRestriction: exclusioncharacteristics[i].timeRestriction }
								: {}
						),
					} as QueryCriterion
					if (i === 0) {
						templateQuery.exclusionCriteria.push([selectedCharacteristic])
					} else {
						if (exclusionLogic[i - 1] === 'or') {
							templateQuery.exclusionCriteria.push([selectedCharacteristic])
							tempIndex++
						} else if (exclusionLogic[i - 1] === 'and') {
							templateQuery.exclusionCriteria[tempIndex].push(selectedCharacteristic)
						}
					}
				}
			}
			return templateQuery
		},

		getDataFromUpload(data: { uploadedCriteria: MachbarkeitQueryData, characteristicsDisplay: SelectedCharacteristics }): void {
			this.clearRunningQuery()
			this.hasDataFromUpload = true
			this.$store.dispatch('getUploadCharacteristics', data.characteristicsDisplay)
			this.queryData = data.uploadedCriteria
		},

		async toggleQuery(data: MachbarkeitQueryData): Promise<void> {
			// Stop Query if it is running
			if (this.isQueryRunning === true) {
				this.clearRunningQuery()
				return
			}
			// Start Query
			this.abortController = new AbortController()
			this.isQueryRunning = true
			const [numberOfPatients, errorMessage] = await getMachbarkeit(data, this.abortController)
			this.numberOfPatients = numberOfPatients
			this.errorMessage = errorMessage
			this.isQueryRunning = false
			this.abortController = null
		},
	},
})
</script>

<style scoped>
.feasibility-query-container {
	display: flex;
	flex-direction: column;
	width: 60%;
	max-width: 1000px;
	height: 100%;
	margin: 0px auto 0px auto;
	padding: 20px 20px 0px 20px;
	overflow-y: auto;
	overflow-x: hidden;
}

.feasibility-query-wrapper {
	display: flex;
	flex-direction: column;
	flex: 1 1 100%;
}

.feasibility-query__output {
	display: flex;
	column-gap: 5%;
	flex-direction: row;
	place-content: center space-between;
	align-items: center;
	margin-bottom: 20px;
	width: 100%;
}

.number-patients {
	display: flex;
	flex-direction: row;
	align-items: center;
	height: 56px;
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 512 512' fill='currentcolor'%3E%3Cpath d='M 504 256 c 0 136.997 -111.043 248 -248 248 S 8 392.997 8 256 C 8 119.083 119.043 8 256 8 s 248 111.083 248 248 Z m -248 50 c -25.405 0 -46 20.595 -46 46 s 20.595 46 46 46 s 46 -20.595 46 -46 s -20.595 -46 -46 -46 Z m -43.673 -165.346 l 7.418 136 c 0.347 6.364 5.609 11.346 11.982 11.346 h 48.546 c 6.373 0 11.635 -4.982 11.982 -11.346 l 7.418 -136 c 0.375 -6.874 -5.098 -12.654 -11.982 -12.654 h -63.383 c -6.884 0 -12.356 5.78 -11.981 12.654 Z'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position-y: center;
	background-position-x: 10px;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	width: 45%;
}

.number-patients p {
	display: flex;
	align-items: center;
	margin-left: 30px;
	font-size: medium;
}

.number-patients span {
	display: flex;
	padding-left: 5px;
}

.error-message {
	color: red;
}

.feasibility-query__button-group {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

button {
	border-radius: 8px;
}
</style>
