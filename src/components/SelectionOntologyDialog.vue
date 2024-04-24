<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="selection-dialog-wrapper">
		<div class="selection-dialog-pane">
			<div class="selection-dialog">
				<h2 class="selection-dialog-title">
					Einschränkungen der ausgewählten Merkmale
					<p>{{ selectedOptionArray }}</p>
				</h2>
				<div class="selection-dialog-panel">
					<SelectedFeatureLimitation v-for="(selectedOntology, index) in selectedOntologyArray"
						:key="selectedOntology.id"
						:ontology="selectedOntology"
						:index-element="index"
						:selected-option-array="selectedOptionArray"
						@delete-dialog-card="deleteDialogCard" />
				</div>

				<div class="dialog-button">
					<button @click="checkOption">
						SPEICHERN
					</button>
					<button @click="$emit('dialog-close')">
						ABBRECHEN
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import SelectedFeatureLimitation from './SelectedFeatureLimitation.vue'
export default {
	name: 'SelectionOntologyDialog',
	components: {
		SelectedFeatureLimitation,
	},
	props: {
		selectedOntologyArray: {
			type: Array,
			default: Array,
		},
	},

	data() {
		return {
			stateIndex: [],
			fileName: '',
			isDialogVisible: true,
			state: false,
			selectedBetween: 'zwischen',
			 /* moment().format('YYYY-MM-DD') */
			toDate: '',
			selectedOptionArray: [],
			limitedInfo: {
				id: null,
				ontologyName: null,
				timeRange: {
					name: 'zwischen',
					fromDate: null,
					toDate: null,
				},
				compareOperator: {
					name: 'kein Filter',
					min: null,
					max: null,
					unit: null,
				},
				gender: null,
			},
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.selectedOntologyArray.forEach((object) => {
			const inputObj = { ...this.limitedInfo }
			inputObj.id = object.id
			inputObj.ontologyName = object.display
			this.selectedOptionArray.push(inputObj)
		})
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		deleteDialogCard(index) {
			this.selectedOntologyArray.splice(index, 1)
		},

		checkOption() {
			/* if all options completely fulfilled */
		},
	},
}
</script>

<style scoped>
@media (max-width: 1300px) {
	.selection-dialog-wrapper {
		top: -40%;
	}
	.selection-dialog {
		max-height: 400px;
	}
}

.selection-dialog-wrapper {
	display: flex;
	position: relative;
	z-index: 100;
	width: 70%;
	top: 20%;
	margin: 0px auto 0px auto;
}

.selection-dialog-pane {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
	max-height: 700px;
}

.selection-dialog {
	display: flex;
	flex-direction: column;
	width: 100%;
	max-height: 700px;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.selection-dialog-title {
	z-index: 1000;
	opacity: 1;
	padding: 5px;
	margin: 0 0 20px!important;
	border-bottom-width: 2px;
	border-bottom-style: solid;
	border-bottom-color: #5a78ae;
	font-weight: 600;
	font-family: Roboto,Helvetica Neue,sans-serif;
}

.selection-dialog-panel {
	overflow-y: auto;
	overflow-x: hidden;
	margin-bottom: 20px;
}

.dialog-button {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

.dialog-button button {
	border-radius: 8px;
}
</style>
