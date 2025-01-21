<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="footer-container">
		<div class="footer__button-group">
			<input id="upload"
				ref="fileInput"
				type="file"
				accept="application/json"
				hidden
				@change="handleFileUpload">
			<label for="upload" class="upload-query-button">
				ABFRAGE LADEN
			</label>
			<button :disabled="!isCriteriaAvailable" @click="$emit('open-save-modal')">
				ABFRAGE SPEICHERN
			</button>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import type { FeasibilityQueryDisplayData } from './FeasibilityQueryDisplay.vue'

export default Vue.extend({
	name: 'FooterContent',
	props: {
		isCriteriaAvailable: {
			type: Boolean,
			default: false,
		},
	},

	methods: {
		handleFileUpload(event: Event) {
			const target = event.target as HTMLInputElement
			if (target.files && target.files.length > 0) {
				const file = target.files[0]
				const reader = new FileReader()
				reader.onload = (e) => {
					try {
						const uploadedCriteria: FeasibilityQueryDisplayData['queryData'] = JSON.parse(e.target?.result as string)
						const isJsonDataValid = (uploadedCriteria.inclusionCriteria && uploadedCriteria.inclusionCriteria?.length > 0) || (uploadedCriteria.exclusionCriteria && uploadedCriteria.exclusionCriteria?.length > 0)
						isJsonDataValid ? this.$emit('get-query-data', uploadedCriteria) : alert('Invalid JSON Format')
					} catch (error) {
						alert('Invalid JSON Format')
					}
				}
				reader.readAsText(file)
			}
			target.value = ''
		},
	},
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

.footer__button-group {
	display: flex;
	flex-direction: row;
	column-gap: 15px;
}

.upload-query-button {
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

.upload-query-button:hover {
	border: 1px solid #006aa3;
}

.upload-query-button:active {
	background-color: white;
}

button {
	border-radius: 8px;
}
</style>
