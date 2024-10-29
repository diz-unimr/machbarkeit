<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div>
		<!-- v-if="filteredCriterion !== null && filteredCriterion.selectable === true" -->
		<div class="ontology-nested-tree-node-wrapper">
			<p class="criterion-code">
				code
			</p>
			<div class="search-tree-term-entry">
				<input :id="String(criterion?.id)"
					v-model="isChecked"
					type="checkbox"
					:value="criterion?.display">
				<p>
					{{ criterion?.display }}
				</p>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeSearchInputData {
	filteredCriterion: OntologyTreeElement | null;
}

export interface CheckedItem {
	action: string;
	node: OntologyTreeElement;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNodeSearchInput',
	components: {},
	props: {
		criterion: {
			type: Object as PropType<OntologyTreeElement>,
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
		}
	},

	computed: {
		isChecked: {
			get(): boolean {
				return this.$store.getters.checkedItems(this.criterion.id)
			},
			set(checked: boolean): void {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion })
					this.$store.dispatch('addCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
					this.$store.dispatch('removeCheckedItem', { id: this.criterion.id, display: this.criterion.display })
				}

			},
		},
	},

	watch: {},

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
		/* filterCriteria(textSearch: string, criterion: OntologyTreeElement): OntologyTreeElement | null {
			// const filteredItem = criterion.termCodes?.[0].code.toLowerCase().includes(textSearch.toLowerCase()) || criterion.termCodes?.[0].display.toLowerCase().includes(textSearch.toLowerCase())
			const filteredItem = true
			if (filteredItem) {
				this.filteredCriterion = criterion
			} else this.filteredCriterion = null
			return this.filteredCriterion
		}, */

		/* checkExistingData() {
			this.$emit('check-existing-data', this.index)
		}, */
	},
})
</script>

<style scoped>
.ontology-nested-tree-node-wrapper {
	display: flex;
	margin-top: 5px;
}

.search-tree-term-entry {
	display: flex;
	align-items: flex-start;
	margin-left: 5px;
	width: 100%;
	height: 100%;
	gap: 20px;
}

.search-tree-term-entry input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin: 0px;
}

.search-tree-term-entry p {
	flex: 1;
	margin-top: 5px;
}

.criterion-code {
	font-weight: 500;
	width: 15%;
	margin-top: 5px;
}
</style>
