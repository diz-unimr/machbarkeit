<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="ontology-nested-tree-node">
		<li style="list-style-type: none;">
			<div class="ontology-head-node">
				<button :class="{'default-cursor': !criterion.children}" @click="() => (state = !state)">
					<img v-if="criterion.children"
						:src="state
							? imgExpand
							: imgCollapse
						">
					<img v-if="!criterion.children"
						class="default-cursor"
						src="">
				</button>
				<div v-if="criterion.selectable === true" class="search-tree-term-entry">
					<input :id="criterion.id"
						v-model="isChecked"
						type="checkbox"
						:value="criterion.display">
					<p @click="() => (state = !state)">
						{{ criterion.display }}
					</p>
				</div>
				<div v-else
					class="search-tree-term-entry">
					<p @click="() => (state = !state)">
						{{ criterion.display }}
					</p>
				</div>
			</div>
			<ul v-show="state">
				<OntologyNestedTreeNode v-for="child in criterion.children"
					:key="child.id"
					:criterion="child"
					@input="checkboxTrigger" />
			</ul>
		</li>
	</div>
</template>

<script lang="ts">
import Vue, { type PropType } from 'vue'
import type { OntologyTreeElement } from '../types/OntologySearchTreeModalData'

interface OntologyNestedTreeNodeData {
	state: boolean;
	imgCollapse: string;
	imgExpand: string;
}

export interface CheckedItem {
	action: string;
	node: OntologyTreeElement;
}

export default Vue.extend({
	name: 'OntologyNestedTreeNode',
	components: {},
	props: {
		isRootNode: {
			type: Boolean,
			default: false,
		},
		criterion: {
			type: Object as PropType<OntologyTreeElement>,
			required: true,
		},
		/* einschlussTextSerach: {
			type: String,
			default: '',
		},
		ausschlussTextSerach: {
			type: String,
			default: '',
		}, */
	},

	data(): OntologyNestedTreeNodeData {
		return {
			state: false,
			imgCollapse: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse-blue.png',
			imgExpand: 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png',
		}
	},

	computed: {
		isChecked: {
			// Determines if the current item is checked
			get(): string {
				return ''
			},
			// Updates checked items when checkbox state changes
			set(checked): void {
				if (checked) {
					this.$emit('input', { action: 'check', node: this.criterion })
				} else {
					this.$emit('input', { action: 'uncheck', node: this.criterion })
				}
			},
		},
	},

	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {
		if (this.isRootNode === true) {
			this.state = true
		}
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
	},
})
</script>

<style scoped>
.ontology-nested-tree-node {
	overflow-y: auto;
	scrollbar-width: auto;
	height: 100%;
	padding-right: 10px;
}

.ontology-head-node {
	flex-direction: row;
	display: flex;
	place-content: center flex-start;
	align-items: flex-start;
	margin-top: 4px;
	gap: 10px;
}

.ontology-head-node button {
	display: flex;
	flex-direction: row;
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
	flex-direction: row;
	align-items: flex-start;
	gap: 10px;
}

.search-tree-term-entry input[type='checkbox'] {
	width: 16px;
	height: 16px;
	margin: 0px 10px;
}

.search-tree-term-entry p {
	margin: 0;
	flex: 1;
	margin-top: 5px;
}

.search-tree-term-entry p:hover {
	cursor: pointer;
}

.default-cursor {
	cursor: default
}

.display-none {
	display: none !important;
}

img {
	height: 15px;
	width: 15px;
}

ul {
	margin-left: 40px;
}
</style>
