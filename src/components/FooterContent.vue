<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div id="footer">
		<div class="button-container">
			<!-- <label style="font-size: 12px; cursor: unset;">Nur JSON Datei erlaubt</label> -->
			<div style="display: flex; flex-direction: row; column-gap: 15px;">
				<input id="upload" type="file" hidden>
				<label for="upload" class="query-upload-button" @click="dialogClose">ABFRAGE LADEN</label>
				<button style="border-radius: 8px;" @click="dialogOpen">
					ABFRAGE SPEICHERN
				</button>
			</div>
		</div>
	</div>
</template>
<script>
export default {
	name: 'FooterContent',
	data() {
		return {
		}
	},
	methods: {
		dialogOpen() {
			this.$emit('dialog-open')
		},
		dialogClose() {
			this.$emit('dialog-close')
		},
	},
}
</script>
<style scoped>
#footer {
	display: flex;
	justify-content: flex-end;
	align-items: flex-end;
	margin-top: 10px;
	/* height: 10%; */
	width: 90%;
	/* padding: 20px; */
}

.button-container {
	display: flex;
	flex-direction: column;
	row-gap: 8px;
	align-items: center;
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
</style>
