<template>
	<div class="selection-dialog-card__content"
		:class="{'card-content__expand': state}">
		<div class="card-content__header">
			<div class="card-content-header__button">
				<button @click="state = !state">
					<img :src="imgExpand"
						:style="{transform: state ? 'rotate(180deg)': 'rotate(0deg)'}">
				</button>
				<p>{{ display }} </p>
				<p v-if="display === 'Zeitraum' || profile.valueDefinition.optional">
					(optional)
				</p>
			</div>
			<div class="card-content-header__reset">
				<button :disabled="isResetDisabled" @click="reset">
					ZURÜCKSETZEN
				</button>
			</div>
		</div>

		<TimeRangeOption v-if="profile.timeRestrictionAllowed"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			@toggle-reset-button="toggleResetButton"
			@get-selected-option="getSelectedOption" />

		<QuantityType v-if="profile.valueDefinition?.type === 'quantity'"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			@toggle-reset-button="toggleResetButton"
			@get-selected-option="getSelectedOption" />

		<ConceptType v-if="profile.valueDefinition?.type === 'concept'"
			:profile="profile"
			:is-reset-disabled="isResetDisabled"
			:selected-ontology="selectedOntology"
			@toggle-reset-button="toggleResetButton"
			@get-selected-option="getSelectedOption" />
	</div>
</template>

<script>
import TimeRangeOption from './TimeRangeOption.vue'
import ConceptType from './ConceptType.vue'
import QuantityType from './QuantityType.vue'
export default {
	name: 'FilterCard',
	components: {
		TimeRangeOption,
		ConceptType,
		QuantityType,
	},
	props: {
		profile: {
			type: Object,
			default: Object,
		},
		attribute: {
			type: String,
			default: String,
		},
		isFilterOptional: {
			type: Boolean,
			default: true,
		},
		selectedOntology: {
			type: Object,
			default: Object,
		},
		display: {
			type: String,
			default: String,
		},
		getSelectedOptions: {
			type: Function,
			default: () => {},
		},
	},
	data() {
		return {
			state: !this.isFilterOptional,
			isResetDisabled: true,
			groupFilter: [],
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
		}
	},

	created() {},
	updated() {},
	methods: {
		setState() {
			if (!this.profile.valueDefinition.optional) {
				this.state = true
			}
		},

		getSelectedOption(data) {
			this.groupFilter.push(data)
			this.$emit('get-selected-options', this.groupFilter)
		},

		toggleResetButton(isReset) {
			this.isResetDisabled = !isReset
		},

		reset() {
			this.isResetDisabled = true
		},
	},
}
</script>

<style scoped>
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

button {
	border-radius: 8px;
}

img {
	height: 16px;
	width: 16px;
}
</style>
