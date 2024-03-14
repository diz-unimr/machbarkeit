<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="machbarkeit-body">
		<AttributeList />
		<FeasibilityQuery />
	</div>
</template>

<script>
import AttributeList from './AttributeList.vue'
import FeasibilityQuery from './FeasibilityQuery.vue'

export default {
	name: 'FeasibilityContent',
	components: {
		AttributeList,
		FeasibilityQuery,
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
}
</script>

<style scoped>
.machbarkeit-body {
	width: 100%;
	height: 97%;
	display: flex;
	flex-direction: column;
	flex-wrap: wrap;
}

</style>
