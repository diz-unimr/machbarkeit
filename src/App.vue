<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<!-- eslint no-trailing-spaces: "error" -->
	<div id="content" class="app-machbarkeit">
		<div id="machbarkeit-container">
			<FeasibilityContent @update-status="querySave" />
		</div>
	</div>
</template>

<script>
import FeasibilityContent from './components/FeasibilityContent.vue'
export default {
	name: 'App',
	components: {
		FeasibilityContent,
	},
	data() {
		return {
			isDialogVisible: false,
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
		querySave() {
			this.isDialogVisible = true
		},
		queryCancel() {
			this.isDialogVisible = false
		},
	},
}
</script>

<style scoped>
#machbarkeit-container {
	display: flex;
	flex-wrap: wrap;
	width: 100%;
	background-color: white;
}
</style>
