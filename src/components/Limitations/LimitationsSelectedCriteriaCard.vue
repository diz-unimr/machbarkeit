<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="selection-dialog-card">
		<div class="selection-dialog-card__header">
			<p style="font-weight: 500;">
				{{ selectedOntology.display }}
			</p>
			<button v-if="!isLimitationEditFeature" class="delete-btn" @click="deleteCard($vnode?.data?.attrs?.id)">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>
		<template v-if="uiProfile != null || uiProfile != undefined">
			<template v-for="(attribute, index) in ['timeRestrictionAllowed', 'valueDefinition']">
				<template v-if="attribute === 'timeRestrictionAllowed' && profile?.timeRestrictionAllowed">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Zeitraum"
						attribute="timeRestrictionAllowed"
						@get-selected-options="getSelectedOptions" />
				</template>
				<template v-if="attribute === 'valueDefinition' && profile?.valueDefinition?.type">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Wertebereich"
						attribute="valueDefinition"
						:selected-ontology="selectedOntology"
						:is-filter-optional="profile.valueDefinition.optional"
						@get-selected-options="getSelectedOptions" />
				</template>
			</template>
		</template>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import FilterCard from './FilterCard.vue'
import type { UiProfile, Profile } from '../../types/FeasibilityQueryBuilderData.ts'
import type { SelectedOptionData } from '../../types/FilterCardData'
import type { OntologyTreeElement } from '../../types/OntologySearchTreeModalData.ts'

interface LimitationsSelectedCriteriaCardData {
    profile: Profile | null,
    imgDelete: string,
}

export default Vue.extend({
	name: 'LimitationsSelectedCriteriaCard',
	components: {
		FilterCard,
	},
	props: {
		uiProfile: {
			type: Object as PropType<UiProfile>,
			required: true,
		},
		selectedOntology: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
		isLimitationEditFeature: {
			type: Boolean,
			default: false,
		},
		getSelectedFeatureFilter: {
			type: Function,
			default: () => {},
		},
		deleteDialogCard: {
			type: Function,
			default: () => {},
		},
	},
	data(): LimitationsSelectedCriteriaCardData {
		return {
			profile: null,
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete.png',
		}
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.getProfile()
		console.log('selectedOntology: ', this.selectedOntology)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
	},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		getProfile(): void {
			if (this.selectedOntology.context.display === 'Diagnose' || this.selectedOntology.context.display === 'Prozedur') {
				this.profile = this.uiProfile[this.selectedOntology.context.display]
			} else {
				this.profile = this.uiProfile[this.selectedOntology.display]
			}
		},

		getSelectedOptions(selectedOptions: SelectedOptionData[]): void {
			selectedOptions.map((obj) => {
				switch (obj.type) {
				case 'conceptType':
					this.$emit('update-selected-ontology', { item: obj }) // { type: 'conceptType', item: obj }
					break
				case 'quantityType':
					this.$emit('update-selected-ontology', { item: obj }) // { type: 'quantityType', item: obj }
					break
				case 'timeRange':
					this.$emit('update-selected-ontology', { item: obj }) // { type: 'timeRange', item: obj }
					break
				default:
				}
				return this.selectedOntology
			})

			console.log('this.selectedOntology: ', this.selectedOntology)
			this.$emit('get-selected-feature-filter', this.selectedOntology)
		},

		validateEmptyInput(): void {

		},

		deleteCard(key: number): void {
			this.$emit('delete-dialog-card', key)
		},
	},
})
</script>

<style scoped>
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
	column-gap: 10%;
	align-items: center;
	justify-content:space-between;
	margin: 0px 10px 20px 10px;
}

.selection-dialog-card__content {
	height: 52px;
	transition: height 0.25s ease;
	box-shadow: 0 3px 1px -2px #adbcd7, 0 2px 2px 0 #adbcd7, 0 1px 5px 0 #adbcd7;
	border-radius: 4px;
	padding: 5px 20px;
	margin-bottom: 20px;
	overflow: hidden;
	position: relative;
}

.card-content__expand {
	height: 100%;
}

.selection-dialog-card__content img {
	transition: all .25s ease-in
}

.card-content__header {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
}

.card-content-header__button {
	display: flex;
	flex-direction: row;
	column-gap: 10px;
	align-items: center;
	justify-content: flex-start;
}

.card-content-header__button button {
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

.card-content-header__reset button {
	font-size: 14px;
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

.from-option {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	column-gap: 10px;
	position: relative;
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
</style>
