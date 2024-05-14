<template>
	<div class="selection-dialog-card">
		<div class="selection-dialog-card__header">
			<p style="font-weight: 500;">
				{{ selectedOntology.display }}
			</p>
			<button class="delete-btn" @click="deleteCard($vnode.data.attrs.id)">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>
		<template v-if="uiProfile != null || uiProfile != undefined">
			<template v-for="(attribute, index) in ['timeRestrictionAllowed', 'valueDefinition']">
				<template v-if="attribute === 'timeRestrictionAllowed' && profile.timeRestrictionAllowed">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Zeitraum"
						attribute="timeRestrictionAllowed"
						@get-selected-options="getSelectedOptions" />
				</template>
				<template v-if="attribute === 'valueDefinition' && profile.valueDefinition?.type">
					<FilterCard :key="attribute + index"
						:profile="profile"
						display="Wertebereicht"
						:selected-ontology="selectedOntology"
						:is-filter-optional="profile.valueDefinition.optional"
						attribute="valueDefinition"
						@get-selected-options="getSelectedOptions" />
				</template>
			</template>
		</template>
	</div>
</template>

<script>
import FilterCard from './FilterCard.vue'

export default {
	name: 'LimitationsSelectedFeaturesCard',
	components: {
		FilterCard,
	},
	props: {
		uiProfile: {
			type: Object,
			default: Object,
		},
		selectedOntology: {
			type: Object,
			default: Object,
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
	data() {
		return {
			profile: null,
			profileFilterInfo: {
				type: null,
				timeRestrictionAllowed: {
					display: 'Zeitraum',
					state: false,
					isResetDisabled: true,
				},
				valueDefinition: {
					display: 'Wertebereich',
					state: false,
					isResetDisabled: true,
					selectedConcepts: null,
					selectedTimeRange: null,
				},
			},
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete.png',
		}
	},

	watch: {
		uiProfile() {
			if (Object.keys(this.uiProfile).length > 0) {
				this.getProfile()
				this.setProfile()
			}
		},
		selectedOntology() {
			this.getProfile()
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
	updated() {
	},
	beforeDestroy() {
	},
	destroyed() {},

	methods: {
		getProfile() {
			if (this.selectedOntology.context.display === 'Diagnose' || this.selectedOntology.context.display === 'Prozedur') {
				this.profile = this.uiProfile[this.selectedOntology.context.display]
			} else {
				this.profile = this.uiProfile[this.selectedOntology.display]
			}
		},

		setProfile() {
			if (this.profile.timeRestrictionAllowed) {
				this.selectedOntology.timeRestriction = null
			}
			if (this.profile.valueDefinition?.type === 'quantity') {
				this.selectedOntology.quantityType = null
			}
			if (this.profile.valueDefinition?.type === 'concept') {
				this.selectedOntology.conceptType = null
			}
		},

		getSelectedOptions(data) {
			data.map((obj) => {
				switch (obj.type) {
				case 'conceptType':
					this.selectedOntology.conceptType = obj
					break
				case 'quantityType':
					this.selectedOntology.quantityType = obj
					break
				case 'timeRange':
					this.selectedOntology.timeRange = obj
					break
				default:
				}
				return this.selectedOntology
			})

			this.$emit('get-selected-feature-filter', this.selectedOntology)
		},

		validateEmptyInput() {

		},

		toggleResetButton(attribute) {
			this.profileFilter[attribute].isResetDisabled = false
		},

		getLimitationInfo(info) {
			this.selectedOptionArray.splice(this.$vnode.key, 1, info)
		},

		deleteCard(key) {
			this.$emit('delete-dialog-card', key)
		},
	},
}
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
	column-gap: 10px;
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
