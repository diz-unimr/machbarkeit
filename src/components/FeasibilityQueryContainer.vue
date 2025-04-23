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
						<span v-if="isQeuryCompleted !== null && !isQeuryCompleted">
							<img src="../../img/loading_spinner.svg">
						</span>
						<template v-else-if="numberOfPatients !== null">
							<span v-if="numberOfPatients <= 3" class="error-message">Das Ergebnis ist zu klein</span>
							<span v-else>{{ numberOfPatients }}</span>
						</template>
						<span v-else-if="errorMessage" class="error-message">{{ errorMessage }}</span>
						<span v-else>-</span>
					</p>
				</div>
				<div class="feasibility-query__button-group">
					<button :disabled="!isCriteriaAvailable" @click="resetSelectedCriteria()">
						ZURÜCKSETZEN
					</button>
					<button :disabled="!isCriteriaAvailable" @click="startQuery(queryData)">
						ABFRAGE STARTEN
					</button>
				</div>
			</div>
			<SaveQueryModal v-if="isSaveModalOpen"
				:query-data="queryData"
				@close-save-modal="closeSaveModal" />

			<FeasibilityQueryBuilder ref="childComponent"
				:uploaded-query-data="queryDataFromUpload"
				:data-from-upload="dataFromUpload"
				:is-criteria-available="isCriteriaAvailable"
				:is-save-modal-open="isSaveModalOpen"
				@get-query-data="getQueryData" />
		</div>
		<MachbarkeitFooter :is-criteria-available="isCriteriaAvailable"
			@open-save-modal="openSaveModal"
			@close-save-modal="closeSaveModal"
			@send-criteria-to-display="forwardCriteriaToDisplay"
			@get-query-data="getQueryData" />
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import FeasibilityQueryBuilder from './FeasibilityQueryBuilder.vue'
import SaveQueryModal from './SaveQueryModal.vue'
import MachbarkeitFooter from './FooterContent.vue'
import type { FeasibilityQueryBuilderData, SelectedCharacteristics } from '../types/FeasibilityQueryBuilderData'
import axios, { AxiosError, type CancelTokenSource } from 'axios'

interface FeasibilityQueryContainerData {
	queryData: FeasibilityQueryBuilderData['queryData'] | null;
	queryDataFromUpload: FeasibilityQueryBuilderData['queryData'] | null;
	dataFromUpload: {
		inclusionCharacteristics: SelectedCharacteristics;
		exclusionCharacteristics: SelectedCharacteristics;
	} | null;
	numberOfPatients: number | null;
	isSaveModalOpen: boolean;
	isCriteriaAvailable: boolean;
	errorMessage: string | null;
	isQeuryCompleted: boolean | null;
	cancelTokenSource: CancelTokenSource | null;
}

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
			queryDataFromUpload: null,
			dataFromUpload: null,
			numberOfPatients: null,
			isSaveModalOpen: false,
			isCriteriaAvailable: false,
			errorMessage: null,
			isQeuryCompleted: null,
			cancelTokenSource: null,
		}
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

		resetSelectedCriteria() {
			this.isCriteriaAvailable = false
			this.numberOfPatients = null
			this.errorMessage = null
			this.isQeuryCompleted = true
			this.cancelTokenSource = axios.CancelToken.source()
			this.cancelTokenSource!.cancel('User stopped the request')
			this.cancelTokenSource = null
		},

		getQueryData(data: FeasibilityQueryBuilderData['queryData'] | null): void {
			this.queryData = data
			this.isCriteriaAvailable = !!data
			this.numberOfPatients = null
			this.errorMessage = null
		},

		forwardCriteriaToDisplay(data: { inclusionCharacteristics: SelectedCharacteristics, exclusionCharacteristics: SelectedCharacteristics }) {
			this.dataFromUpload = data
		},

		async startQuery(data: FeasibilityQueryContainerData['queryData']) {
			this.isQeuryCompleted = false
			this.numberOfPatients = null
			this.cancelTokenSource = axios.CancelToken.source()

			try {
				const response = await axios.post('https://feasibility.diz.uni-marburg.de/query/execute',
					JSON.stringify(data),
					{
						headers: {
							'Content-Type': 'application/json',
						},
					},
				)
				this.numberOfPatients = response.data
				this.isQeuryCompleted = true
			} catch (error) {
				const errorMessage = ((error as AxiosError).response!.data as { error: string }).error
				const errorText = 'None of the following contextual term codes'

				if (errorMessage.startsWith(errorText)) {
					const labCodeError = [...errorMessage.matchAll(/code=([^,\]]+)/g)].map(m => m[1]).filter(item => item !== 'Laboruntersuchung')
					const labCodeErrorAlert = labCodeError.length > 1
						? labCodeError.slice(0, -1).join(', ') + ' und ' + labCodeError[labCodeError.length - 1]
						: labCodeError[0] || ''
					const textAlert = labCodeError.length === 1 ? ' wurde im Codebaum nicht gefunden.' : ' wurden im Codebaum nicht gefunden.'
					alert(labCodeErrorAlert + textAlert + ' Bitte kontaktieren Sie den DIZ-Support.')
				}
				this.errorMessage = error ? 'Found some error!' : null
			}
			this.isQeuryCompleted = true
		},
	},
})
</script>

<style scoped>
.feasibility-query-container {
	display: flex;
	flex-direction: column;
	width: 55%;
	max-width: 1200px;
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
