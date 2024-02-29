<template>
	<div class="save-overlay-wrapper">
		<div class="save-overlay-pane">
			<div class="save-dialog-container">
				<h2 class="save-dialog-title">
					Abspeichern der aktuellen Suchanfrage
				</h2>
				<div class="save-query-box">
					<NcTextField :value.sync="fileName"
						label="Dateiname"
						trailing-button-icon="close"
						placeholder=" "
						:show-trailing-button="fileName !== ''"
						@trailing-button-click="fileName = ''" />
				</div>
				<div class="save-button-container">
					<button style="border-radius: 8px;">
						SPEICHERN
					</button>
					<button style="border-radius: 8px;" @click="hideDialog">
						ABBRECHEN
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'

export default {
	name: 'SaveQuery',
	components: {
		NcTextField,
	},
	data() {
		return {
			fileName: '',
			isDialogVisible: true,
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
		hideDialog() {
			this.$emit('update-status')
		},
	},
}
</script>

<style>
.save-overlay-wrapper {
	display: flex;
	position: absolute;
	z-index: 1000;
	justify-content: center;
	align-items: center;
	width: 73%;
	top: 20%;
	left: 25%;
}

.save-overlay-pane {
	position: static;
	pointer-events: auto;
	box-sizing: border-box;
	display: flex;
	max-width: 100%;
	max-height: 100%;
	justify-content: center;
	align-items: center;
}

.save-dialog-container {
	display: block;
	width: 100%;
	height: 100%;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.save-dialog-title {
	z-index: 1000;
	opacity: 1;
	padding: 5px;
	margin: 0 0 20px!important;
	border-bottom-width: 2px;
	border-bottom-style: solid;
	border-bottom-color: #5a78ae;
	font-weight: 500;
	font-family: Roboto,Helvetica Neue,sans-serif;
}

.save-query-box {
	min-width: 480px;
	margin-bottom: 30px;
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: center space-around;
	align-items: center;
}

.file-name-input {
	border: 1px solid #c0c7ce;
	border-radius: 10px;
	display: inline-flex;
	align-items: baseline;
	box-sizing: border-box;
	width: 100%;
	padding: 10px;
}

.input-field__input {
	border: 1px solid #c0c7ce !important;
}

.save-button-container {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 8px;
}
</style>
