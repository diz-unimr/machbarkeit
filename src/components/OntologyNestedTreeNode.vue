<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<keep-alive>
			<li>
				<div class="ontology-head-node">
					<button v-if="!criterion?.leaf" @click="expandTreeNode()">
						<img v-if="!criterion?.leaf"
							:src="state
								? imgExpand
								: imgCollapse
							">
						<div v-else
							class="img-display-none" />
					</button>
					<div v-if="criterion?.selectable" class="search-tree-term-entry">
						<input :id="String(criterion?.id)"
							v-model="isChecked"
							type="checkbox"
							:value="criterion?.display">
						<p class="swl-code">
							{{ findSWLCODE(criterion) }}
						</p>
						<p class="swl-description" @click="() => (state = !state)">
							{{ criterion?.display }}
						</p>
					</div>
					<div v-else-if="!criterion?.selectable" class="search-tree-term-entry">
						<p class="swl-code">
							HK
						</p>
						<p class="swl-desciption" @click="() => (state = !state)">
							{{ criterion?.display }}
						</p>
					</div>
				</div>
				<ul v-show="state">
					<template v-if="criterion.children">
						<OntologyNestedTreeNode v-for="child in criterion.children"
							:key="child.id"
							:criterion="child"
							@input="checkboxTrigger" />
					</template>
				</ul>
			</li>
		</keep-alive>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeData {
	state: boolean;
	imgCollapse: string;
	imgExpand: string;
	concepts: [
		{
			display: string;
			id: number;
			leaf: boolean;
			moduleId: number;
			parentId: number | null;
			selectable: boolean;
		}
	] | null;
	swlCode: string | undefined;
}

export interface CheckedItem {
	action: string;
	node: Criterion;
	swlCode?: string;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNode',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<Criterion>,
			default: null,
		},
	},

	data(): OntologyNestedTreeNodeData {
		return {
			state: false,
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
			concepts: null,
			swlCode: undefined,
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get(): boolean {
				return this.$store.getters.checkedItems(this.criterion.id)
			},
			// Updates checked items when checkbox state changes
			set(checked: boolean): void {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion, swlCode: this.swlCode })
					this.$store.dispatch('addCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
					this.$store.dispatch('removeCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {

	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		checkboxTrigger(checkedItem: CheckedItem): void {
			this.$emit('input', checkedItem)
		},

		expandTreeNode(): void {
			this.state = !this.state
		},

		findSWLCODE(criterion: Criterion): string | undefined {
			// const loinc = criterion.termCodes.find((termCode) => termCode.system === 'http://loinc.org')
			// const swlcode = criterion.termCodes.find((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
			// return [loinc?.code, swlcode?.code]
			this.swlCode = String(criterion)
			this.swlCode = 'swl-code'
			return this.swlCode
		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding-right: 10px;
	margin-left: 20px;
}

.ontology-nested-tree-node li {
	list-style-type: none;
}

.ontology-head-node {
	display: flex;
	place-content: center flex-start;
	align-items: flex-start;
	margin-top: 5px;
	gap: 15px;
}

.ontology-head-node button {
	display: flex;
	align-items: center;
	width: auto;
	text-decoration: none;
	background-color: white;
	border: none;
	outline: none;
	margin: 0px;
	padding: 0px;
}

.search-tree-term-entry {
	display: flex;
	align-items: flex-start;
	gap: 15px;
	width: 100%;
}

.search-tree-term-entry input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin: 0px;
}

.search-tree-term-entry .swl-code {
	width: 9%;
	margin-top: 5px;
	font-weight: 500;
}

.search-tree-term-entry .swl-description {
	max-width: 100%;
	margin-top: 5px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.img-display-none {
	cursor: default;
	height: 15px;
	width: 15px;
}

img {
	height: 15px;
	width: 15px;
}

ul {
	margin-left: 20px;
}
</style>
