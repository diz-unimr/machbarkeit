<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-container">
		<div class="feasibility-query-wrapper">
			<div class="feasibility-query__result-output">
				<div class="number-patients">
					<p>Anzahl der Patienten: {{ numberOfPatients }}</p>
				</div>
				<div class="query-button-container">
					<button :disabled="true">
						ZURÜCKSETZEN
					</button>
					<button>
						ABFRAGE STARTEN
					</button>
				</div>
			</div>
			<SaveQueryDialog v-if="isDialogOpen" @dialog-close="querySaveDiaglogClose" />
			<FeasibilityQueryBuilder />
		</div>
		<MachbarkeitFooter @dialog-open="querySaveDialogOpen" @dialog-close="querySaveDiaglogClose" />
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import FeasibilityQueryBuilder from './FeasibilityQueryBuilder.vue'
import SaveQueryDialog from './SaveQueryDialog.vue'
import MachbarkeitFooter from './FooterContent.vue'

interface FeasibilityQueryContainerData {
	numberOfPatients: number;
	isDialogOpen: boolean;
}

export default Vue.extend({
	name: 'FeasibilityQueryContainer',
	components: {
		FeasibilityQueryBuilder,
		SaveQueryDialog,
		MachbarkeitFooter,
	},

	data(): FeasibilityQueryContainerData {
		return {
			numberOfPatients: 0,
			isDialogOpen: false,
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
		querySaveDialogOpen(): void {
			this.isDialogOpen = true
		},

		querySaveDiaglogClose(): void {
			this.isDialogOpen = false
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

.feasibility-query__result-output {
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

.query-button-container {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

button {
	border-radius: 8px;
}
</style>
