<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="save-query-modal-container">
		<div class="save-query-modal-wrapper">
			<h2 class="save-query-modal__title">
				Abspeichern der aktuellen Suchanfrage
			</h2>
			<div class="save-query-modal__folder-name">
				<NcTextField :value.sync="fileName"
					label="Dateiname"
					trailing-button-icon="close"
					placeholder=" "
					:show-trailing-button="fileName !== ''"
					@trailing-button-click="fileName = ''" />
			</div>
			<div class="save-query-modal__button-container">
				<button>
					SPEICHERN
				</button>
				<button @click="$emit('close-save-modal')">
					ABBRECHEN
				</button>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import { NcTextField } from '@nextcloud/vue'

interface SaveQueryModalData {
	fileName:string;
	isModalVisible: boolean;
}

export default Vue.extend({
	name: 'SaveQueryModal',
	components: {
		NcTextField,
	},
	data(): SaveQueryModalData {
		return {
			fileName: '',
			isModalVisible: true,
		}
	},

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

	methods: {},
})
</script>

<style scoped>
.save-query-modal-container {
	display: flex;
	position: relative;
	z-index: 100;
	margin: 0px auto 0px auto;
	top: 15%;
	width: 50%;
}

.save-query-modal-wrapper {
	display: flex;
	flex-direction: column;
	position: absolute;
	width: 100%;
	padding: 24px;
	background-color: white;
	border-radius: 5px;
	box-shadow: 0 2px 4px -1px #0003, 0 4px 5px #00000024, 0 1px 10px #0000001f;
}

.save-query-modal__title {
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

.save-query-modal__folder-name {
	display: flex;
	flex-direction: column;
	place-content: center space-around;
	margin-bottom: 30px;
}

.save-query-modal__button-container {
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
