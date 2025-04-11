/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		checkedItemsSet: new Set(),
		checkedItemsMap: new Map(),
	},

	mutations: {
		ADD_CHECKED_ITEM(state, item) {
			const checkedItemsMapTemp = new Map(state.checkedItemsMap) // Create a new Set instance
			if (!checkedItemsMapTemp.has(item.id)) {
				checkedItemsMapTemp.set(item.id, item.node)
			}
			state.checkedItemsMap = checkedItemsMapTemp // Replace the old Set with a new one
		},

		REMOVE_CHECKED_ITEM(state, node) {
			const checkedItemsMapTemp = new Map(state.checkedItemsMap) // Create a new Set instance
			checkedItemsMapTemp.delete(node.id) // Remove the item from the Set
			state.checkedItemsMap = checkedItemsMapTemp // Replace the old Set with a new one
		},

		CLEAR_CHECKED_ITEM(state) {
			state.checkedItemsSet = new Set() // Clear the checked items
			state.checkedItemsMap = new Map() // Clear the checked items
		},

		ADD_OPENED_ITEM(state, id) {
			if (!state.openedItems.some(item => item.id === id)) {
				state.openedItems.push(id)
			}
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
			commit('CLEAR_CHECKED_ITEM')
		},
	},

	getters: {
		getCheckedItems: (state) => (node) => {
			return state.checkedItemsMap.has(node.id)
		},
	},
})
