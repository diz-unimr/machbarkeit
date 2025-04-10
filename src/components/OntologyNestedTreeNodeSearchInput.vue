<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div>
		<div class="ontology-nested-tree-node-wrapper">
			<input :id="String(criterion?.id)"
				v-model="isChecked"
				:disabled="onlySwlCode"
				type="checkbox"
				:value="criterion?.display">
			<div class="search-tree-term-entry">
				<div class="swl-wrapper" :style="{gap: swlCode ? '1.5%' : '0'}">
					<p class="swl-code">
						{{ swlCode }}
					</p>
					<p class="swl-description" :style="{cursor: criterion.leaf ? 'default' : 'pointer'}">
						{{ criterion?.display }}
					</p>
				</div>
				<p v-if="onlySwlCode && criterion.selectable === true" class="swl-code-warning">
					(Die Suche nach SWL-Code wird momentan nicht unterstützt)
				</p>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { Criterion } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeSearchInputData {
	filteredCriterion: Criterion | null;
	onlySwlCode: boolean;
	swlCode: string | undefined;
}

export interface CheckedItem {
	action: string;
	node: Criterion;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNodeSearchInput',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<Criterion>,
			default: null,
		},
		index: {
			type: Number,
			default: Number,
		},
		searchInputText: {
			type: String,
			default: null,
		},
	},

	data(): OntologyNestedTreeNodeSearchInputData {
		return {
			filteredCriterion: null,
			swlCode: undefined,
			onlySwlCode: false,
		}
	},

	computed: {
		isChecked: {
			get(): boolean {
				return this.$store.getters.getCheckedItems(this.criterion)
			},
			set(checked: boolean): void {
				if (checked) {
					this.$store.dispatch('addCheckedItem', this.criterion)
				} else {
					this.$store.dispatch('removeCheckedItem', this.criterion)
				}

			},
		},
	},

	watch: {},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		this.findSWL(this.criterion)
	},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		findSWL(criterion: Criterion): void {
			const onlySwlCode = criterion.termCodes?.every((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
			// const loinc = criterion.termCodes?.find((termCode) => termCode.system === 'http://loinc.org' || termCode.system === 'http://snomed.info/sct')
			const swlcode = criterion.termCodes?.find((termCode) => termCode.system === 'https://fhir.diz.uni-marburg.de/CodeSystem/swisslab-code')
			this.onlySwlCode = onlySwlCode
			this.swlCode = swlcode?.code ?? undefined
		},
	},
})
</script>

<style scoped>
.ontology-nested-tree-node-wrapper {
	display: flex;
	margin-top: 5px;
	gap: clamp(10px, 1.5%, 15px);
}

.ontology-nested-tree-node-wrapper input[type='checkbox']:disabled {
	cursor: default;
}

.search-tree-term-entry {
	display: flex;
	flex-direction: column;
	width: 100%;
}

.search-tree-term-entry .swl-description {
	max-width: fit-content;
	margin-top: 5px;
}

.search-tree-term-entry .swl-code {
	margin-top: 5px;
	font-weight: 500;
	font-size: 14px;
}

.search-tree-term-entry input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin: 0px;
}

.swl-wrapper {
	display: flex;
	gap: 1.5%;
	/* gap: clamp(10px, 1.5%, 15px); */
}

.swl-code-warning {
	font-size: 12px;
	color: #DC3545;
	cursor: default;
}

.criterion-code {
	font-weight: 500;
	width: 12%;
	margin-top: 5px;
}
</style>
