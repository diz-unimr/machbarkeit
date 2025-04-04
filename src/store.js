/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		checkedItems: [], // new Map() // new Set()
	},

	mutations: {
		ADD_CHECKED_ITEM(state, item) {
			// Array
			if (!state.checkedItems.some(obj => obj.id === item.node.id)) {
				state.checkedItems.push(item.node)
			}

			/* // Map()
			const updatedCheckedItems1 = new Map(state.checkedParents) // Create a new Set instance
			if (item.isChecked && !updatedCheckedItems1.has(item.node.id)) {
				updatedCheckedItems1.set(item.node.id, item.node)
			}
			state.checkedParents = updatedCheckedItems1 // Replace the old Set with a new one

			// Set()
			const updatedCheckedItems = new Set(state.checkedItems) // Create a new Set instance
			if (item.isChecked && !updatedCheckedItems.has(item.node.id)) {
				updatedCheckedItems.add(item.node.id)
			}
			state.checkedItems = updatedCheckedItems // Replace the old Set with a new one */
		},

		REMOVE_CHECKED_ITEM(state, item) {
			// Array
			state.checkedItems = state.checkedItems.filter(obj => {
				return obj.id !== item.id
			})

			/* // Set()
			const updatedCheckedItems = new Set(state.checkedItems) // Create a new Set instance
			if (!item.isChecked && updatedCheckedItems.has(item.id)) {
				updatedCheckedItems.delete(item.id)
			}
			state.checkedItems = updatedCheckedItems // Replace the old Set with a new one */
		},

		CLEAR_CHECKED_ITEM(state) {
			state.checkedItems = []
			// state.checkedItems = new Set() // Clear the checked items
			state.checkedParents = new Map() // Clear the checked items
		},
	},

	actions: {
		addCheckedItem({ commit }, item) {
			commit('ADD_CHECKED_ITEM', item)
		},

		removeCheckedItem({ commit }, item) {
			commit('REMOVE_CHECKED_ITEM', item)
		},

		clearCheckedItem({ commit }) {
			commit('CLEAR_CHECKED_ITEM')
		},

	},

	getters: {
		getCheckedItems: (state) => (id) => {
			// return state.checkedItems.has(id)
			return state.checkedItems.some(item => item.id === id)
		},
	},
})
