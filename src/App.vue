<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<!-- eslint no-trailing-spaces: "error" -->
	<div id="content" class="app-machbarkeit">
		<div id="container">
			<FeasibilityContent v-if="isMachbarkeitContent" />
			<MyQueryContent v-if="isMeineAbfragenContent" />
			<FooterContent :ismachbarkeit="isMachbarkeitContent" :ismeineabfragen="isMeineAbfragenContent" @update-status="contentChange" />
		</div>
	</div>
</template>

<script>
import FeasibilityContent from './components/FeasibilityContent.vue'
import MyQueryContent from './components/MyQueryContent.vue'
import FooterContent from './components/FooterContent.vue'

export default {
	name: 'App',
	components: {
		FeasibilityContent,
		MyQueryContent,
		FooterContent,
	},
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
		/* this.getCsv() */
	},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		contentChange() {
			this.isMachbarkeitContent = !this.isMachbarkeitContent
			this.isMeineAbfragenContent = !this.isMeineAbfragenContent
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
