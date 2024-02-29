<template>
	<div id="feasibility-query">
		<div id="feasibility-query__content">
			<SaveQuery v-if="isDialogVisible" ref="saveQueryRef" @update-status="queryCancel" />
			<div id="feasibility-query__input">
				<div class="top">
					<div class="number-patients">
						<p style="margin-left: 30px; font-size: medium;">
							Anzahl der Patienten: 5
						</p>
					</div>
					<div class="button-container">
						<button style="border-radius: 8px;">
							ZURÜCKSETZEN
						</button>
						<button style="border-radius: 8px;">
							ABFRAGE STARTEN
						</button>
					</div>
				</div>
				<div id="consent-container">
					<div style="font-size: 16px; font-weight: 500; color: #5270a7;">
						Art der Datennutzung
					</div>
					<div class="consent-radio-button">
						<input v-model="selectedOption"
							type="radio"
							value="broad-consent"
							name="broad-consent"
							:checked="selectedOption === 'broad-consent'">
						<div class="consent-text">
							<a class="consent-link" href="https://www.medizininformatik-initiative.de/de/mustertext-zur-patienteneinwilligung">Broad Consent (der MII oder MII-kompatibel)</a>
							voraussetzen (Daten können zentral zusammengeführt werden)
							<div class="show-tooltip">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="question-circle"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z" />
								</svg>
								<span class="tooltiptext">Bei der zentralen Analyse erhalten Sie von der MII pseudonymisierte Daten zu den Patienten, die zu Ihrem Forschungsprojekt passen. Pseudonyme Daten (die durch die Pseudonyme automatisch einen Bezug zu den Patienten ermöglichen) können aber nur genutzt werden, wenn die Patienten ihre Einwilligung in die Nutzung erteilt haben. Die Forschenden bekommen die pseudonymisierten Daten zum Download zur Verfügung gestellt und können darin ihre Analysen mit großer Flexibilität und Tiefe sowie häufig mit größerem Tempo ausführen, als es bei verteilten Analysen</span>
							</div>
						</div>
					</div>
					<div class="consent-radio-button">
						<input v-model="selectedOption"
							type="radio"
							value="no-broad-consent"
							name="broad-consent"
							:checked="selectedOption === 'no-broad-consent'">
						<div class="consent-text">
							Kein „Broad Consent“ voraussetzen (Daten stehen nur für „Verteilte Analysen“ zur Verfügung)
							<div class="show-tooltip">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="question-circle"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z" />
								</svg>
								<span class="tooltiptext">Bei verteilten Analysen findet die Analyse auf den Daten an den Standorten statt. Die Forschenden erhalten anonyme Ergebnisdaten. An vielen Standorten kann bei verteilten Analysen auf einer anderen Rechtsgrundlage als der Einwilligung der Patienten gerechnet werden. Dadurch wird häufig eine größere Menge an Patienten gefunden, die zu Ihrer Forschungsfrage passen. Bei manchen Standorten erfolgen Machbarkeitsanfragen auch auf Basis der Patienteneinwilligung. Diese Standorte antworten auf</span>
							</div>
						</div>
					</div>
				</div>
				<div id="in-exclusion-criteria">
					<div class="container-search-input">
						<div class="header-label">
							Einschlusskriterien
						</div>
						<div class="search-container">
							<div class="search-button">
								<button class="button-view">
									<svg role="img"
										aria-hidden="true"
										focusable="false"
										data-prefix="fas"
										data-icon="folder"
										class="svg-inline--fa fa-folder fa-w-16 fa-2x"
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 512 512">
										<path fill="currentColor"
											d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
									</svg>
								</button>
							</div>
							<div class="search-input">
								<NcTextField :value.sync="text1"
									style="font-size: 16px;"
									label="Code oder Suchbegriff eingeben"
									trailing-button-icon="close"
									placeholder=" "
									:show-trailing-button="text1 !== ''"
									@trailing-button-click="text1 = ''">
									<Magnify :size="20" />
								</NcTextField>
							</div>
						</div>
					</div>
					<div class="pipe" />
					<div class="container-search-input">
						<div class="header-label">
							Ausschlusskriterien
						</div>
						<div class="search-container">
							<div class="search-button">
								<button class="button-view">
									<svg role="img"
										aria-hidden="true"
										focusable="false"
										data-prefix="fas"
										data-icon="folder"
										class="svg-inline--fa fa-folder fa-w-16 fa-2x"
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 512 512">
										<path fill="currentColor"
											d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
									</svg>
								</button>
							</div>
							<div class="search-input">
								<NcTextField :value.sync="text2"
									style="font-size: 16px;"
									label="Code oder Suchbegriff eingeben"
									trailing-button-icon="close"
									placeholder=" "
									:show-trailing-button="text2 !== ''"
									@trailing-button-click="text2 = ''">
									<Magnify :size="20" />
								</NcTextField>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="feasibility-query__output">
				<div class="card-header">
					<div class="text-header">
						<label style="font-size: 16px; font-weight: 500;">Ausgewählte Merkmale</label>
					</div>
				</div>
				<div class="card-content">
					<div class="textfield" />
					<div class="pipe2" />
					<div class="textfield" />
				</div>
			</div>
		</div>
		<MachbarkeitFooter @dialog-open="querySaveDialogOpen" @dialog-close="querySaveDiaglogClose" />
	</div>
</template>

<script>
import SaveQuery from './SaveQuery.vue'
import MachbarkeitFooter from './FooterContent.vue'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify'

export default {
	name: 'FeasibilityQuery',
	components: {
		SaveQuery,
		MachbarkeitFooter,
		NcTextField,
		Magnify,
	},
	data() {
		return {
			text1: '',
			text2: '',
			text3: 'hunter',
			text4: '',
			text5: '',
			boardConsent: '',
			noBoardConsent: '',
			selectedOption: 'no-broad-consent',
			isDialogVisible: false,
		}
	},
	computed: {},
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
		clearText(text) {
			text = ''
		},
		querySaveDialogOpen() {
			this.isDialogVisible = true
		},
		querySaveDiaglogClose() {
			this.isDialogVisible = false
		},
		queryCancel() {
			this.isDialogVisible = false
		},

	},
}
</script>

<style scoped>
.wrapper {
	display: flex;
	gap: 4px;
	align-items: flex-end;
	flex-wrap: wrap;
}

#feasibility-query {
	width: 73%;
	height: 100%;
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	flex-wrap: wrap;
	place-content: flex-start center;
	align-items: flex-start;
	align-content: space-between;
	padding: 20px 20px 0px 20px;
	overflow-y: auto;
	overflow-x: hidden;
}

#feasibility-query__content {
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: flex-start center;
	align-items: flex-start;
	flex: 1 1 100%;
	max-width: 90%;
}

#feasibility-query__input {
	position: relative;
	top: 0;
	width: 100%;
	margin-bottom: 20px;
}

#feasibility-query__output {
	border: 1px solid #9ea9b3;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	box-shadow: none!important;
	width: 100%;
}

.card-header {
	display: flex;
	padding-left: 10px;
	background: #5270a7;
	color: #ffffff;
	border-top-right-radius: 4px;
	border-top-left-radius: 4px;
	min-height: 3.5em;
}

.text-header {
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-around;
	align-items: stretch;
}

.top {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: center space-between;
	align-items: center;
}

.number-patients {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: center space-between;
	align-items: center;
	height: 3.5em;
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 512 512' fill='currentcolor'%3E%3Cpath d='M 504 256 c 0 136.997 -111.043 248 -248 248 S 8 392.997 8 256 C 8 119.083 119.043 8 256 8 s 248 111.083 248 248 Z m -248 50 c -25.405 0 -46 20.595 -46 46 s 20.595 46 46 46 s 46 -20.595 46 -46 s -20.595 -46 -46 -46 Z m -43.673 -165.346 l 7.418 136 c 0.347 6.364 5.609 11.346 11.982 11.346 h 48.546 c 6.373 0 11.635 -4.982 11.982 -11.346 l 7.418 -136 c 0.375 -6.874 -5.098 -12.654 -11.982 -12.654 h -63.383 c -6.884 0 -12.356 5.78 -11.981 12.654 Z'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position-y: center;
	background-position-x: 10px;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
	width: 40%;
}

.button-container {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

#consent-container {
	margin: 10px 0 15px 5px;
}

.consent-radio-button {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
}

.consent-link {
	color:#5270a7; text-decoration: underline;
}

.consent-text {
	display: flex;
	align-items: center;
	flex-direction: row;
	column-gap: 5px;
}

svg {
	width: 15px;
	height: 15px;
}

.show-tooltip {
	position: relative;
	display: inline-block;
}

.show-tooltip .tooltiptext {
	display: flex;
	visibility: hidden;
	width: 300px;
	height: auto;
	background-color: white;
	padding: 10px;
	position: absolute;
	z-index: 100;
	left: 150%;
	top: -160%;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.show-tooltip:hover .tooltiptext {
	visibility: visible;
}

#in-exclusion-criteria {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-between;
	align-items: stretch;
}

.container-search-input {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 48.5%;
	position: relative;
	min-height: 6em;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
}

.header-label {
	min-width: min-content;
	background-color: #5270a7;
	color: #ffffff;
	text-align: center;
	font-size: 16px;
	font-weight: 500;
	padding: 2px 0;
}

.search-container {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	justify-content: space-around;
	align-items: center;
	margin: 10px;
}

.search-button {
	place-content: stretch center;
	align-items: stretch;
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
}

.button-view {
	border-radius: 4px;
	border-width: 1px;
	border-style: solid;
	margin-left: 5%;
	height: 44px;
	width: 44px;
	line-height: unset;
	color: white;
	background-color: #5e6c78;
}

.button-view:hover {
	background-color: #9ea9b3;
}

.button-view:active {
	background-color: #5e6c78 !important;
	color: white !important;
}

.search-input {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 85%;
	font-size: 14px;
}

.input-field {
	margin-block-start: 0px;
}

#code-search-input {
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 512 512' fill='gray'%3E%3Cpath d='M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z'%3E%3C/path%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position-y: center;
	background-position-x: 10px;
	width: 100%;
	margin-bottom: 20px;
	border: 1px solid #c0c7ce;
	padding-left: 35px;
}

.pipe {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 1%;
	background-color: #5270a7;
	border-radius: 4px;
}

.pipe2 {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 1%;
	background-color: #5270a7;
}

.card-content {
	display: flex;
	flex-direction: row;
	min-height: 150px;
}

.card-content2 {
	padding-bottom: 16px;
	display: block;
}

.textfield2 {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-between;
	align-items: stretch;
	height: 100%;
	max-width: 100%;
	flex: 1 1 100%;
}

.textfield {
	flex: 1 1 100%;
	border: dashed 1px black;
	margin: 20px;
	padding: 10px;
}
</style>
