<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="footer-container">
		<div class="load-save-button">
			<input id="upload" type="file" hidden>
			<label for="upload" class="query-upload-button" @click="$emit('dialog-close')">
				ABFRAGE LADEN
			</label>
			<button @click="$emit('dialog-open')">
				ABFRAGE SPEICHERN
			</button>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
	name: 'FooterContent',
})
</script>

<style scoped>
.footer-container {
	display: flex;
	justify-content: flex-end;
	align-items: flex-end;
	margin-top: 20px;
	width: 100%;
}

.footer-wrapper {
	display: flex;
	flex-direction: column;
	row-gap: 8px;
	align-items: center;
}

.load-save-button {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

.query-upload-button {
	border-radius: 8px;
	font-weight: bold;
	padding: 8px 14px;
	margin: 3px 3px 3px 0;
	width: auto;
	min-height: 36px;
	cursor: pointer;
	box-sizing: border-box;
	background-color: #ededed;
	border: 1px solid #dbdbdb;
	color: #222222;
	outline: none;
}

.query-upload-button:hover {
	border: 1px solid #006aa3;
}

.query-upload-button:active {
	background-color: white;
}

button {
	border-radius: 8px;
}
</style>
