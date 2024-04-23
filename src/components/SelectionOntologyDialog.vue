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
				</h2>
				<div class="selection-dialog-panel">
					<SelectedFeatureLimitation v-for="(selectedOntology, index) in selectedOntologyArray"
						:key="selectedOntology.id"
						:selected-ontology="selectedOntology"
						:index-element="index"
						@delete-dialog-card="deleteDialogCard" />
					<!-- <div v-for="(selectedOntology, index) in selectedOntologyArray" :key="selectedOntology.id" class="selection-dialog-card">
						<div class="selection-dialog-card__header">
							<p style="font-weight: 500;">
								{{ selectedOntology.display }}
							</p>
							<button class="delete-btn" style="" @click="deleteDialogCard(index)">
								Löschen
								<img :src="imgDelete">
							</button>
						</div>

						<div class="selection-dialog-card__content"
							:class="{'card-content__expand': state}">
							<div>
								<div class="content-header">
									<button :id="selectedOntology.display"
										:label="selectedOntology.display"
										@click="() => state = !state">
										<img :src="imgExpand"
											:style="{transform: state === true ? 'rotate(180deg)': 'rotate(0deg)'}">
									</button>
									<p>Zeitraum (option)</p>
								</div>
								<div class="content-header__option">
									<select :id="index" v-model="selectedBetween" class="content-option option__between">
										<option value="zwischen">
											zwischen
										</option>
										<option value="am">
											am
										</option>
										<option value="vor">
											vor
										</option>
										<option value="nach">
											nach
										</option>
									</select>
									{{ inputDate }}
									<input v-model="fromDate[index]"
										class="content-option"
										type="date">
									<input :id="'to-date-' + selectedOntology.display"
										v-model="toDate"
										class="content-option"
										type="date">
								</div>
							</div>
						</div>

						<div class="selection-dialog-card__footer">
							Footer
						</div>
					</div> -->
				</div>

				<div class="dialog-button">
					<button>
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
			inputDate: [],
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			imgArrowDown: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-down.png',
			imgCalendar: 'http://localhost:8080/apps-extra/machbarkeit/img/calendar.png',
			imgClose: 'http://localhost:8080/apps-extra/machbarkeit/img/close-black.png',
			imgClose2: 'http://localhost:8080/apps-extra/machbarkeit/img/close2.png',
		}
	},

	computed: {
		fromDate: {
			get() {
				return this.inputDate
			},
			set(dateValue) {
				this.inputDate = dateValue
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		/* stateChange(id) {
			this.state = !this.state
			if (this.state) {
				this.stateIndex[id] = id
			} else {
				this.stateIndex[id] = -1
			}
		}, */

		deleteDialogCard(index) {
			this.selectedOntologyArray.splice(index, 1)
		},

	},
}
</script>

<style scoped>
.selection-dialog-wrapper {
	display: flex;
	position: relative;
	z-index: 100;
	width: 70%;
	top: 20%;
	margin: 0px auto 0px auto;
}

@media (max-width: 1300px) {
	.selection-dialog-wrapper {
		top: -40%;
	}
	.selection-dialog {
		max-height: 400px;
	}
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

.selection-dialog-card {
	display: flex;
	flex-direction: column;
	place-content: center space-around;
	padding: 20px;
	margin: 20px;
	box-shadow: 0 3px 3px -2px #0003, 0 3px 4px #00000024, 0 1px 8px #0000001f;
	border-radius: 4px;
}

.selection-dialog-card__header {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
	justify-content:space-between;
	margin: 0px 10px 20px 10px;
}

.selection-dialog-card__content {
	height: 50px;
	transition: height 0.25s ease;
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 5px 20px;
	margin-bottom: 20px;
	overflow: hidden;
	position: relative;
}

.card-content__expand {
	height: 150px;
}

.selection-dialog-card__content img {
	transition: all .25s ease-in
}

.content-header {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
	justify-content: flex-start;
}

.delete-btn {
	display: flex;
	flex-direction: row;
	column-gap: 5px;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
	background-color: white;
	align-items: center;
}

.content-header__option {
	display: flex;
	flex-direction: row;
	column-gap: 20px;
	margin: 10px 0;
}

.from-option {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	column-gap: 10px;
	position: relative;
}

.content-option {
	width: 30%;
	height: 50px !important;
	border-radius: 8px;
	border: 1px solid grey !important;
}

.between__head{
	display: flex;
	flex-direction: row;
	width: 100%;
	border: 1px solid grey;
	padding: 12px;
	border-radius: 8px;
	justify-content: space-between;
	align-items: center;
	cursor: pointer;
}

.between__dropdown {
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
	background: white;
	width: 100%;
	max-height: 275px;
	outline: 0;
	margin: 0;
	list-style-type: none;
	position: sticky;
}

.content-header__option img {
	height: 14px;
	width: 14px;
}

.selection-dialog-card__footer {
	margin: 0px 10px 20px 10px;
}

.dialog-button {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

button {
	border-radius: 8px;
}

img {
	height: 16px;
	width: 16px;
}

.delete-btn img {
	width: 25px;
	height: 20px;
}

.content-header button {
	display: flex;
	flex-direction: row;
	align-items: center;
	width: auto;
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
}
</style>
