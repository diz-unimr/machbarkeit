<template>
	<div id="footer">
		<div v-if="ismachbarkeit">
			<button style="border-radius: 8px;" @click="updateStatus">
				ABFRAGE LADEN
			</button>
			<button style="border-radius: 8px;" @click="updateStatus">
				ABFRAGE SPEICHERN
			</button>
		</div>
		<!-- @click="contentChange" -->
		<div v-if="ismeineabfragen">
			<button style="border-radius: 8px;" @click="updateStatus">
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

</style>
