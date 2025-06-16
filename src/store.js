/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		checkedItems: new Set(),
		selectedItems: new Set(),
	},

	mutations: {
		ADD_CHECKED_ITEM(state, id) {
			const newSet = new Set(state.checkedItems) // Create a new Set instance
			if (!newSet.has(id)) {
				newSet.add(id)
			}
			state.checkedItems = newSet // Replace the old Set with a new one
		},

		REMOVE_CHECKED_ITEM(state, id) {
			const newSet = new Set(state.checkedItems) // Create a new Set instance
			newSet.delete(id) // Remove the item from the Set
			state.checkedItems = newSet // Replace the old Set with a new one
		},

		CLEAR_CHECKED_ITEMS(state) {
			state.checkedItems = new Set() // Clear the checked items
		},

		ADD_SELECTED_ITEM(state, { item, color }) {
			const selectedItems = item
			selectedItems.color = color // Add color to the item
			const newSet = new Set(state.selectedItems) // Create a new Set instance
			if (!newSet.has(selectedItems)) {
				newSet.add(selectedItems)
			}
			state.selectedItems = newSet // Replace the old Set with a new one
		},

		REMOVE_SELECTED_ITEM(state, id) {
			const newSet = new Set(state.selectedItems) // Create a new Set instance
			newSet.delete(id) // Remove the item from the Set
			state.selectedItems = newSet // Replace the old Set with a new one
		},

		CLEAR_SELECTED_ITEMS(state) {
			state.selectedItems = new Set() // Clear the checked items
		},
	},

	actions: {
		addCheckedItem({ commit }, id) {
			commit('ADD_CHECKED_ITEM', id)
		},

		removeCheckedItem({ commit }, id) {
			commit('REMOVE_CHECKED_ITEM', id)
		},

		clearCheckedItems({ commit }) {
			commit('CLEAR_CHECKED_ITEMS')
		},

		addSelectedItem({ commit }, { item, color }) {
			commit('ADD_SELECTED_ITEM', { item, color })
		},

		removeSelectedItem({ commit }, item) {
			commit('REMOVE_SELECTED_ITEM', item)
		},

		clearSelectedItems({ commit }) {
			commit('CLEAR_SELECTED_ITEMS')
		},
	},

	getters: {
		getCheckedItems: (state) => (id) => {
			return state.checkedItems.has(id)
		},
	},
})
