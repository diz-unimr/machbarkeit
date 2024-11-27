<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-container">
		<div class="feasibility-query-wrapper">
			<div class="feasibility-query__output">
				<div class="number-patients">
					<p>Anzahl der Patienten: {{ numberOfPatients ?? '-' }}</p>
				</div>
				<div class="feasibility-query__button-group">
					<button :disabled="true">
						ZURÜCKSETZEN
					</button>
					<button :disabled="hasNoQuery" @click="startQuery(queryData)">
						ABFRAGE STARTEN
					</button>
				</div>
			</div>
			<SaveQueryModal v-if="isSaveModalOpen" @close-save-modal="closeSaveModal" />
			<FeasibilityQueryBuilder :is-save-modal-open="isSaveModalOpen"
				@enable-start-query-button="enableStartQueryButton"
				@get-query-data="getQueryData" />
		</div>
		<MachbarkeitFooter @open-save-modal="openSaveModal" @close-save-modal="closeSaveModal" />
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { generateUrl } from '@nextcloud/router'
import nextcloudAxios from '@nextcloud/axios'
import FeasibilityQueryBuilder from './FeasibilityQueryBuilder.vue'
import SaveQueryModal from './SaveQueryModal.vue'
import MachbarkeitFooter from './FooterContent.vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'
import type { ConceptType } from '../types/ConceptOptionsData'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRangeType } from '../types/TimeRangeOptionsData'

interface FeasibilityQueryContainerData {
	queryData: {
		inclusionCriteria: Criterion[] | undefined,
		exclusionCriteria: Criterion[] | undefined,
	} | null;
	queryData2: {
		version: string;
		display: string;
		inclusionCriteria: [
			Array<ConceptType | QuantityType | TimeRangeType>
		];
	};
	numberOfPatients: number | null;
	isSaveModalOpen: boolean;
	hasNoQuery: boolean;
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
			queryData2: {
				version: '123',
				display: 'Test Query',
				inclusionCriteria: [[]],
			},
			numberOfPatients: null,
			isSaveModalOpen: false,
			hasNoQuery: true,
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

		enableStartQueryButton(hasData: boolean): void {
			this.hasNoQuery = !hasData
		},

		getQueryData(data): void {
			console.log('data', data)
			this.queryData2 = data.inclusionCriteria
			/* console.log('data', data)

			data?.inclusionCriteria?.forEach(item => {
				const selectedCriterion = item.selectedCriterion as ConceptType | QuantityType |TimeRangeType
				selectedCriterion.termCodes = selectedCriterion.termCodes.flat()
				this.queryData2.inclusionCriteria[0].push(selectedCriterion)
			})
			console.log(this.queryData2)
			console.log(this.queryData2.inclusionCriteria[0][1]) */
			/* data?.inclusionCriteria?.map((item, index) => {
				const x = item.selectedCriterion as ConceptType | QuantityType | TimeRangeType
				return this.queryData2.inclusionCriteria[index] = x
			}) */
			/* const inclusion = data?.inclusionCriteria
				? {
					inclusionCriteria: [
						5,
					],
				}
				: {}

			console.log(data)
			const jsonData = {
				version: 'http://to_be_decided.com/draft-1/schema#',
				display: '',
			} */
		},

		async startQuery(data: FeasibilityQueryContainerData['queryData']) {
			this.numberOfPatients = null
			console.log('Start')
			console.log(this.queryData2)
			const response = await nextcloudAxios.post(generateUrl('/apps/machbarkeit/machbarkeit/get_request'),
				{ criteria: JSON.stringify(this.queryData2) },
				{
					headers: {
						'Content-Type': 'application/json',
					},
				})
			// const x = JSON.parse(response.data)
			console.log(response)
			console.log('finished')
			this.numberOfPatients = response.data as number
		},
	},
})
</script>

<style scoped>
.feasibility-query-container {
	display: flex;
	flex-direction: column;
	width: 1200px;
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
	width: 40%;
}

.number-patients p {
	margin-left: 30px;
	font-size: medium;
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
