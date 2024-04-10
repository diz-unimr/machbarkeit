<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="save-overlay-wrapper">
		<div class="save-overlay-pane">
			<div class="save-dialog">
				<h2 class="save-dialog-title">
					Abspeichern der aktuellen Suchanfrage
				</h2>
				<div class="save-dialog-folder-name">
					<NcTextField :value.sync="fileName"
						label="Dateiname"
						trailing-button-icon="close"
						placeholder=" "
						:show-trailing-button="fileName !== ''"
						@trailing-button-click="fileName = ''" />
				</div>
				<div class="save-dialog-button">
					<button>
						SPEICHERN
					</button>
					<button @click="hideDialog">
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
	name: 'SaveQueryDialog',
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

<style scoped>
.save-overlay-wrapper {
	display: flex;
	position: sticky;
	z-index: 1000;
	width: 50%;
	top: 20%;
	margin: 0px auto 0px auto;
}

.save-overlay-pane {
	display: flex;
	position: absolute;
	pointer-events: auto;
	width: 100%;
}

.save-dialog {
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

.save-dialog-folder-name {
	display: flex;
	flex-direction: column;
	place-content: center space-around;
	margin-bottom: 30px;
}

.save-dialog-button {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-end;
	column-gap: 15px;
}

button {
	border-radius: 8px;
}
</style>
