<template>
	<div id="body">
		<AttributeList></AttributeList>
		<FeasibilityQuery></FeasibilityQuery>
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
#body {
	width: 100%;
	height: 90%;
	display: flex;
	flex-direction: column;
	flex-wrap: wrap;
}
</style>
