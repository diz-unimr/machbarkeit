<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div id="content" class="app-machbarkeit">
		<div id="machbarkeit" class="machbarkeit">
			<div class="machbarkeit machbarkeit__body">
				<AttributeList />
				<FeasibilityQueryContainer />
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue from 'vue'
import AttributeList from './components/AttributeList.vue'
import FeasibilityQueryContainer from './components/FeasibilityQueryContainer.vue'

export default Vue.extend({
	name: 'App',
	components: {
		AttributeList,
		FeasibilityQueryContainer,
	},
	data() {
		return {}
	},

	computed: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		const root = document.getElementsByTagName('html')[0]
		root.setAttribute('class', 'webkit chrome win js')
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {},
})
</script>

<style>
/* force ui in dark mode as light mode */
/* :root {
	--color-main-background: #ffffff !important;
	--color-main-text: #222222 !important;
	--color-background-dark: #ededed !important;
	--color-background-light: #ffffff !important;
	--color-border-dark: #dbdbdb !important;
	--icon-triangle-s-dark: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZlcnNpb249IjEuMSIgdmlld2JveD0iMCAwIDE2IDE2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Im00IDYgNCA0IDQtMy45OTR6IiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+Cg==) !important;
} */

html body {
	font-size: 16px !important;
}

button {
	font-size: 16px !important;
}

.machbarkeit {
	display: flex;
	width: 100%;
	background-color: white;
}

.machbarkeit__body {
	height: 97%;
}

.input-field__input {
	border: 1px solid #c0c7ce !important;
}

input[type=checkbox] {
	cursor: pointer !important;
}

</style>
