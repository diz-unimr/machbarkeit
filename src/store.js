/**
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		checkedItems: [], // Store checked state of items by their IDs
	},
	mutations: {
		ADD_CHECKED_ITEM(state, item) {
			if (!state.checkedItems.some(obj => obj.id === item.id)) {
				state.checkedItems.push(item)
			}
		},

		REMOVE_CHECKED_ITEM(state, item) {
			state.checkedItems = state.checkedItems.filter(obj => obj.id !== item.id)
		},

		CLEAR_CHECKED_ITEM(state) {
			state.checkedItems = []
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
		checkedItems: (state) => (id) => {
			return state.checkedItems.some(item => item.id === id)
		},
	},
})
