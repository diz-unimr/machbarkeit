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

<style>
html body {
	font-size: 16px;
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
</style>
