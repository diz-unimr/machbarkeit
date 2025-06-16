<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->

	<div class="ontology-search-tree-container" :style="{ '--theme-color': currentTheme}">
		<div class="ontology-search-tree-wrapper">
			<div class="criteria-type">
				{{ criteriaType }}
			</div>
			<div v-if="modules && modules.length > 0">
				<div class="ontology-search-tree__tabs">
					<div class="ontology-search-tree__tabs-container">
						<button v-for="module in modules"
							:key="module.id"
							:class="['ontology-tab', { 'active': activeModule?.id === module.id }]"
							:style="{ 'background-color': module.color }"
							@click="changeTab(module.id)">
							{{ module.name }}
						</button>
					</div>
				</div>
				<div class="ontology-search-tree__display">
					<div v-if="ontologyTree && !isLoading">
						<div v-if="ontologyTree.length > 0 && ontologyTree[0].moduleId === activeModule?.id">
							<div class="ontology-search-tree__body">
								<div class="module-name">
									{{ modules.filter(module => module.id === activeModule?.id)[0].name }}
								</div>
								<OntologyTreeNode v-for="criterion in ontologyTree"
									:key="criterion.id"
									:module-name="modules.filter(module => module.id === activeModule?.id)[0].name"
									:criterion="criterion"
									:current-theme="currentTheme" />
							</div>
						</div>
						<div v-else-if="ontologyTree?.length === 0" class="no-result-data">
							Keine Daten
						</div>
					</div>
					<div v-else-if="isLoading" class="loading-text">
						Loading...
					</div>
				</div>

				<div class="ontology-search-tree__button-group">
					<button :disabled="isCheckboxEmpty" @click="submit(criteriaType)">
						AUSWÄHLEN
					</button>
					<button @click="cancel(criteriaType)">
						ABBRECHEN
					</button>
				</div>
				<WarningModal v-if="!isCheckboxEmpty && isWarningModalOpened"
					class="warning-modal"
					@submit-change-tab="submitChangeTab"
					@cancel-change-tab="cancelChangeTab" />
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import OntologyTreeNode from './OntologyTreeNode.vue'
import WarningModal from './WarningModal.vue'
import type { OntologySearchTreeModalData, Module, Criterion } from '../types/OntologySearchTreeModalData.ts'

export default Vue.extend({
	name: 'OntologySearchTreeModal',
	components: {
		OntologyTreeNode,
		WarningModal,
	},
	props: {
		criteriaType: {
			type: String,
			default: '',
		},
		modules: {
			type: Array as PropType<Module[]>,
			default: undefined,
		},
		ontologyTree: {
			type: Array as PropType<Criterion[]>,
			default: null,
		},
		isLoading: {
			type: Boolean,
			default: true,
		},
		getSelectedCriteria: {
			type: Function,
			default: () => {},
		},
	},

	data(): OntologySearchTreeModalData {
		return {
			activeModule: undefined,
			nextModule: undefined,
			requestStatus: undefined,
			selectedItems: [],
			checkedItems: undefined,
			isSearchResultNoData: [],
			context: null,
			isCheckboxChecked: false,
			isWarningModalOpened: false,
			currentTheme: 'default',
		}
	},

	computed: {
		isCheckboxEmpty() {
			return this.$store.state.checkedItems.size < 1
		},
	},

	watch: {
		/* searchInputText: {
			async handler(newVal: string) {
				if (newVal.length > 1) {
					this.ontologyTree = await this.getOntology(this.activeModule!, this.searchInputText)
				} else if (newVal.length === 0) {
					this.ontologyTree = await this.getOntology(this.activeModule!)
				}
			},
		}, */
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (!this.activeModule && this.modules) {
			this.activeModule = this.modules[0]
		}
		if (this.modules) {
			this.currentTheme = this.activeModule.color || this.modules[0].color
		}
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
		/*
		if (!this.activeModule && this.modules) {
			this.activeModule = this.modules[0]
		}
		if (this.modules) {
			this.currentTheme = this.activeModule.color || this.modules[0].color
		} */
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		changeTab(moduleId: string): void {
			this.nextModule = this.modules?.filter(module => module.id === moduleId)[0]
			if (this.activeModule.id !== moduleId) {
				if (this.activeModule && !this.isCheckboxEmpty) {
					this.isWarningModalOpened = true
				} else {
					this.activeModule = this.nextModule
					this.currentTheme = this.activeModule.color
					this.activeModule && this.$emit('update-ontology-tree', this.activeModule)
				}
			}
		},

		submit(criteriaType: string): void {
			const selectedItems: Criterion[] = [...this.$store.state.selectedItems]
			this.$emit('get-selected-criteria', criteriaType, selectedItems)
			this.$store.dispatch('clearCheckedItems')
			this.$store.dispatch('clearSelectedItems')
		},

		cancel(criteriaType: string): void {
			this.$emit('toggle-ontology-search-tree-modal', criteriaType)
			this.$store.dispatch('clearCheckedItems')
			this.$store.dispatch('clearSelectedItems')

		},

		submitChangeTab(): void {
			this.isWarningModalOpened = false
			this.activeModule = this.nextModule
			this.currentTheme = this.activeModule.color
			this.$emit('update-ontology-tree', this.activeModule)
			this.$store.dispatch('clearCheckedItems')
			this.$store.dispatch('clearSelectedItems')
		},

		cancelChangeTab(): void {
			this.isWarningModalOpened = false
		},
	},
})
</script>

<style scoped>
.ontology-search-tree-container {
	display:flex;
	z-index: 100;
	max-width: 100%;
	max-height: 100%;
	position: relative;
}

.ontology-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding: 0px 10px;
}

.ontology-search-tree-wrapper {
	display: flex;
	flex-direction: column;
	position: absolute;
	min-height: 190px;
	background-color: #fff;
	box-shadow: 0px 10px 15px 0px #0003, 0px 0px 25px 2px #00000024, 0px 0px 10px 0px #0000001f;
	pointer-events: auto;
	width: 100%;
}

.criteria-type {
	text-align: center;
	margin: 10px 0px;
	font-size: 18px;
	font-weight: bold;
	text-transform: capitalize;
}

.ausschlusskriterien-overlay {
	right: 0;
}

.ontology-search-tree__tabs {
	display: flex;
	flex-direction: row;
	place-content: stretch flex-start;
	align-items: stretch;
	border-top: 2px solid #adbcd7;
	border-bottom: 2px solid #adbcd7;
	overflow-x: auto;
	padding: 3px 0px;
}

.ontology-search-tree__tabs-container {
	display: flex;
	margin: auto;
	padding: 0px 30px;
}

.ontology-search-tree__tabs-container button {
	background-color: #ffffff;
	color: #ffffff;
	border: unset !important;
	border-radius: 5px;
	padding: 8px;
	margin: 10px;
	text-align: center;
	font-weight: normal;
	cursor: pointer;
	text-wrap: nowrap;
	min-width: 70px;
}

/* .ontology-search-tree__tabs-container button:hover {
	border: unset !important;
} */

/* .ontology-search-tree__tabs-container button:active {
	color: #ffffff;
} */

.ontology-tab {
	padding: 10px 20px;
	cursor: pointer;
	border-bottom: none;
	/* border-color: unset !important; */
}

.ontology-tab:active {
	color: #ffffff !important;
	/* border: 3px solid var(--theme-color) !important; */
	/* filter: brightness(85%); */
}

.ontology-tab:hover {
	/* opacity: 0.85; */
	filter: brightness(105%)
}

.ontology-search-tree__display {
	/* min-height: 450px;
	max-height: 650px; */
	height: 580px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	margin: 30px;
}

.ontology-search-tree__body {
	height: 580px;
	overflow-y: auto;
	/* margin: 30px 25px 20px 30px; */
}

/* .ontology-search-tree__body div {
	color: var(--theme-color)
} */

.module-name {
	font-size: 18px;
	font-weight: bold;
	margin-bottom: 20px;
}

.ontology-search-tree__button-group {
	display: flex;
	column-gap: 15px;
	padding: 20px;
	justify-content: flex-end;
	flex-direction: row;
	background-color: var(--theme-color);
}

.ontology-search-tree__button-group button {
	border-radius: 8px;
}

.loading-text {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 100%;
	/* border-top: 2px solid #adbcd7; */
}

.no-result-data {
	text-align: center;
	font-size: large;
	margin: 100px;
}

.modal__content {
	margin: 50px;
	text-align: center;
}

.warning-modal {
	position: absolute;
	top: 40%;
	left: 50%;
	transform: translate(-50%, -50%);
}
</style>
