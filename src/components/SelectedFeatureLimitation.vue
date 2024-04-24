<template>
	<div class="selection-dialog-card">
		<div class="selection-dialog-card__header">
			<p style="font-weight: 500;">
				{{ ontology.display }}
			</p>
			<button class="delete-btn" @click="deleteCard">
				Löschen
				<img :src="imgDelete">
			</button>
		</div>

		<div class="selection-dialog-card__content"
			:class="{'card-content__expand': state}">
			<div class="content-header">
				<button :id="ontology.display"
					:label="ontology.display"
					@click="() => state = !state">
					<img :src="imgExpand"
						:style="{transform: state === true ? 'rotate(180deg)': 'rotate(0deg)'}">
				</button>
				<p>Zeitraum (option)</p>  <!-- option condition -->
			</div>
			<div class="content-header__option">
				<select :id="indexElement"
					v-model="timeRangeOption"
					class="content-option option__between"
					@change="addLimitationOption('timeRange', timeRangeOption)">
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
				<input id="formDate"
					v-model="fromDate"
					class="content-option"
					type="date"
					name="formDate"
					@change="addLimitationOption('fromDate', fromDate)">

				<input id="toDate"
					v-model="toDate"
					class="content-option"
					type="date"
					name="toDate"
					@change="addLimitationOption('toDate', toDate)">
			</div>
		</div>
	</div>
</template>
<script>
export default {
	name: 'SelectedFeatureLimitation',
	components: {},
	props: {
		ontology: {
			type: Object,
			default: Object,
		},
		indexElement: {
			type: Number,
			default: Number,
		},
		deleteDialogCard: {
			type: Function,
			default: () => {},
		},
		selectedOptionArray: {
			type: Array,
			default: Array,
		},
	},
	data() {
		return {
			state: false,
			timeRangeOption: 'zwischen',
			fromDate: '', /* moment().format('YYYY-MM-DD') */
			toDate: '',
			limitedInfo: {
				id: this.ontology.id,
				ontologyName: this.ontology.display,
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
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			imgDelete: 'http://localhost:8080/apps-extra/machbarkeit/img/delete.png',
		}
	},

	computed: {},

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
		addLimitationOption(attrName, value) {
			switch (attrName) {
			/* time range */
			case 'timeRange':
				this.limitedInfo.timeRange.name = value
				break
			case 'fromDate':
				this.limitedInfo.timeRange.fromDate = value
				break
			case 'toDate':
				this.limitedInfo.timeRange.toDate = value
				break
			/* compare operator */
			case 'compareOperator':
				this.limitedInfo.compareOperator.name = value
				break
			case 'min':
				this.limitedInfo.compareOperator.min = value
				break
			case 'max':
				this.limitedInfo.compareOperator.max = value
				break
			case 'unit':
				this.limitedInfo.compareOperator.unit = value
				break
			/* gender */
			case 'gender':
				this.limitedInfo.gender = value
				break
			}

			if (this.limitedInfo.compareOperator.name === 'kein Filter') {
				this.limitedInfo.compareOperator = Object.fromEntries(Object.entries(this.limitedInfo.compareOperator).map(([key]) => [key, null]))
			}
			/* insert object into array */
			this.selectedOptionArray.splice(this.indexElement, 1, this.limitedInfo)
		},

		deleteCard() {
			this.$emit('delete-dialog-card', this.indexElement)
			/* delete object from array */
			this.selectedOptionArray.splice(this.indexElement, 1)
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
