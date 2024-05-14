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
					<LimitationsSelectedFeaturesCard v-for="(selectedOntology, index) in selectedOntologyArray"
						:id="index"
						:key="selectedOntology.id"
						:ui-profile="uiProfile"
						:selected-ontology="selectedOntology"
						@get-selected-feature-filter="getSelectedFeatureFilter"
						@delete-dialog-card="deleteDialogCard" />
				</div>

				<div class="dialog-button">
					<button :disabled="!isFilterComplete" @click="submit">
						HINZUFÜGEN
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
import LimitationsSelectedFeaturesCard from './LimitationsSelectedFeaturesCard.vue'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

export default {
	name: 'LimitationsSelectedFeatures',
	components: {
		LimitationsSelectedFeaturesCard,
	},
	props: {
		selectedOntologyArray: {
			type: Array,
			default: Array,
		},
		getFilterInfo: {
			type: Function,
			default: () => {},
		},
	},

	data() {
		return {
			filterInfo: [],
			notEmptyProfileName: [],
			uiProfile: null,
			isFilterComplete: false,
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	async created() {
		const response = await axios.get(generateUrl('/apps/machbarkeit/machbarkeit/ui_profile'))
		this.uiProfile = response.data
		this.getNotEmptyProfile(this.uiProfile)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		getNotEmptyProfile(uiProfile) {
			const profileKeys = Object.keys(uiProfile)

			for (let i = 0; i < profileKeys.length; i++) {
				const key = profileKeys[i]
				if (uiProfile[key].valueDefinition?.optional === false) {
					this.notEmptyProfileName.push(uiProfile[key].name)
				}
			}
		},

		deleteDialogCard(index) {
			this.selectedOntologyArray.splice(index, 1)
			this.filterInfo.splice(index, 1)
			if (this.selectedOntologyArray.length === 0) {
				this.$emit('dialog-close')
			}
		},

		getSelectedFeatureFilter(filteredCriteria) {
			const filter = {
				display: filteredCriteria.display,
				conceptType: filteredCriteria.conceptType,
				quantityType: filteredCriteria.quantityType,
				timeRange: filteredCriteria.timeRange,
			}

			const index = this.filterInfo.findIndex(item => item.display === filter.display)
			if (index === -1) {
				this.filterInfo.push(filter)
			} else this.filterInfo[index] = filter

			this.checkCompleteFilter()
		},

		checkCompleteFilter() {
			const notCompleteFilter = this.filterInfo.filter(item => {
				let notComplete = false
				for (const key in item) {
					if (item[key] && key !== 'display') {
						notComplete = item[key].isFilterOptional ? false : !item[key].completeFilter
						if (notComplete) {
							return notComplete
						}
					}
				}
				return notComplete
			})

			if (notCompleteFilter.length > 0) {
				this.isFilterComplete = false
			} else this.isFilterComplete = true
		},

		submit() {
			this.$emit('get-filter-info', this.filterInfo)
			this.$emit('dialog-close')
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
	width: 85%;
	/* top: 20%; */
	margin: 0px auto 0px auto;
}

.selection-dialog-pane {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
	max-height: 850px;
}

.selection-dialog {
	display: flex;
	flex-direction: column;
	width: 100%;
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
