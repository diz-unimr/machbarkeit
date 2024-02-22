<template>
	<div id="footer">
		<div v-if="ismachbarkeit">
			<button class="button" @click="updateStatus">
				Gehen zu "Meine Abfragen"
			</button>
		</div>
		<!-- @click="contentChange" -->
		<div v-if="ismeineabfragen">
			<button class="button" @click="updateStatus">
				Genhen zu "Machbarkeitsabfrage"
			</button>
		</div>
	</div>
</template>
<script>
export default {
	name: 'FooterContent',
	props: {
		ismachbarkeit: Boolean,
		ismeineabfragen: Boolean,
	},
	data() {
		return {
			isMachbarkeitContent: Boolean,
			isMeineAbfragenContent: Boolean,
		}
	},
	methods: {
		updateStatus() {
			this.isMachbarkeitContent = this.ismachbarkeit
			this.isMeineAbfragenContent = this.ismeineabfragen
			this.$emit('update-status')
		},
	},
}
</script>
<style scoped>
#footer {
	display: flex;
	justify-content: flex-end;
	height: 10%;
	width: 100%;
	padding: 20px;
	left: 0px;
}

.button {
	border-radius: 8px;
}
</style>
