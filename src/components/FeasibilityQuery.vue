<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="feasibility-query-container">
		<div class="feasibility-query">
			<SaveQueryDialog v-if="isDialogOpen" ref="saveQueryRef" @update-status="queryCancel" />
			<div class="feasibility-query__top">
				<div class="number-patients">
					<p>Anzahl der Patienten: {{ numberOfPatients }}</p>
				</div>
				<div class="query-button">
					<button>
						ZURÜCKSETZEN
					</button>
					<button>
						ABFRAGE STARTEN
					</button>
				</div>
			</div>
			<!-- <div class="feasibility-query__consent">
				<div class="consent-title">
					Art der Datennutzung
				</div>
				<div class="consent-text">
					<input v-model="selectedOption"
						type="radio"
						value="broad-consent"
						name="broad-consent"
						:checked="selectedOption === 'broad-consent'">
					<div>
						<a class="consent-link" :href="patientConsentLink">
							{{ boardConsent1 }}
						</a>
						{{ boardConsent2 }}
						<div class="consent-tooltip">
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
							<span>{{ boardConsentTooltip }}</span>
						</div>
					</div>
				</div>
				<div class="consent-text">
					<input v-model="selectedOption"
						type="radio"
						value="no-broad-consent"
						name="broad-consent"
						:checked="selectedOption === 'no-broad-consent'">
					<div>
						{{ noBoardConsent }}
						<div class="consent-tooltip">
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
							<span>{{ noBoardConsentTooltip }}</span>
						</div>
					</div>
				</div>
			</div> -->
			<FeasibilityQueryBuilder />
		</div>
		<MachbarkeitFooter @dialog-open="querySaveDialogOpen" @dialog-close="querySaveDiaglogClose" />
	</div>
</template>

<script>
import FeasibilityQueryBuilder from './FeasibilityQueryBuilder.vue'
import SaveQueryDialog from './SaveQueryDialog.vue'
import MachbarkeitFooter from './FooterContent.vue'

export default {
	name: 'FeasibilityQuery',
	components: {
		FeasibilityQueryBuilder,
		SaveQueryDialog,
		MachbarkeitFooter,
	},
	data() {
		return {
			boardConsent1: 'Broad Consent (der MII oder MII-kompatibel)',
			boardConsent2: 'voraussetzen (Daten können zentral zusammengeführt werden)',
			boardConsentTooltip: 'Bei der zentralen Analyse erhalten Sie von der MII pseudonymisierte Daten zu den Patienten, die zu Ihrem Forschungsprojekt passen. Pseudonyme Daten (die durch die Pseudonyme automatisch einen Bezug zu den Patienten ermöglichen) können aber nur genutzt werden, wenn die Patienten ihre Einwilligung in die Nutzung erteilt haben. Die Forschenden bekommen die pseudonymisierten Daten zum Download zur Verfügung gestellt und können darin ihre Analysen mit großer Flexibilität und Tiefe sowie häufig mit größerem Tempo ausführen, als es bei verteilten Analysen',
			noBoardConsent: 'Kein „Broad Consent“ voraussetzen (Daten stehen nur für „Verteilte Analysen“ zur Verfügung)',
			noBoardConsentTooltip: 'Bei verteilten Analysen findet die Analyse auf den Daten an den Standorten statt. Die Forschenden erhalten anonyme Ergebnisdaten. An vielen Standorten kann bei verteilten Analysen auf einer anderen Rechtsgrundlage als der Einwilligung der Patienten gerechnet werden. Dadurch wird häufig eine größere Menge an Patienten gefunden, die zu Ihrer Forschungsfrage passen. Bei manchen Standorten erfolgen Machbarkeitsanfragen auch auf Basis der Patienteneinwilligung. Diese Standorte antworten auf',
			selectedOption: 'no-broad-consent',
			patientConsentLink: 'https://www.medizininformatik-initiative.de/de/mustertext-zur-patienteneinwilligung',
			isDialogOpen: false,
			numberOfPatients: 0,
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
			this.isDialogOpen = true
		},
		querySaveDiaglogClose() {
			this.isDialogOpen = false
		},
		queryCancel() {
			this.isDialogOpen = false
		},

	},
}
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

.feasibility-query {
	display: flex;
	flex-direction: column;
	flex: 1 1 100%;
}

.feasibility-query__top {
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

.query-button {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

.feasibility-query__consent {
	margin: 10px 0 15px 5px;
}

.consent-title {
	font-size: 16px;
	font-weight: 500;
	color: #5270a7;
}

.consent-text {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
}

.consent-link {
	color:#5270a7;
	text-decoration: underline;
}

.consent-tooltip {
	position: relative;
	display: inline-block;
}

.consent-tooltip span {
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

.consent-tooltip:hover span {
	visibility: visible;
}

button {
	border-radius: 8px;
}

svg {
	width: 15px;
	height: 15px;
}
</style>
