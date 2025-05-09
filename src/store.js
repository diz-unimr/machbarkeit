/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		checkedItemsMap: new Map(),
	},

	mutations: {
		ADD_CHECKED_ITEM(state, item) {
			const newMap = new Map(state.checkedItemsMap) // Create a new Set instance
			if (!newMap.has(item.id)) {
				newMap.set(item.id, item.node)
			}
			state.checkedItemsMap = newMap // Replace the old Set with a new one
		},

		REMOVE_CHECKED_ITEM(state, node) {
			const newMap = new Map(state.checkedItemsMap) // Create a new Set instance
			newMap.delete(node.id) // Remove the item from the Set
			state.checkedItemsMap = newMap // Replace the old Set with a new one
		},

		CLEAR_CHECKED_ITEMS(state) {
			state.checkedItemsMap = new Map() // Clear the checked items
		},
	},

	actions: {
		addCheckedItem({ commit }, item) {
			commit('ADD_CHECKED_ITEM', item)
		},

		removeCheckedItem({ commit }, id) {
			commit('REMOVE_CHECKED_ITEM', id)
		},

		clearCheckedItem({ commit }) {
			commit('CLEAR_CHECKED_ITEMS')
		},
	},

	getters: {
		getCheckedItems: (state) => (node) => {
			return state.checkedItemsMap.has(node.id)
		},
	},
})
