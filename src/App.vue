<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<!-- eslint no-trailing-spaces: "error" -->
	<div id="content" class="app-machbarkeit">
		<div id="container">
			Container
		</div>
	</div>
</template>

<script>
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {},
	data() {
		return {
			isMachbarkeitContent: true,
			isMeineAbfragenContent: false,
		}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {},
	beforeMount() {},
	mounted() {
		this.getCsv()
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		async getCsv() {
			await axios.get(generateUrl('/apps/machbarkeit/api/0.1/machbarkeit/metadata'))
		},
	},
}
</script>

<style scoped>
#container {
	display: flex;
	flex-wrap: wrap;
	width: 100%;
	background-color: white;
}
</style>
