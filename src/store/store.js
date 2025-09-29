/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
 */
import Vue, { set } from 'vue'
import Vuex, { Store } from 'vuex'

Vue.use(Vuex)

export default new Store({
	state: {
		modules: undefined,
		ontologies: undefined,
		checkedItems: [],
		selectedItems: {},
		filterOptionsItem: new Map(),
		selectedCharacteristics: {
			inclusionCriteria: {
				characteristics: [],
				logic: [],
			},
			exclusionCriteria: {
				characteristics: [],
				logic: [],
			},
		},
		criteriaType: '',
	},

	mutations: {
		ADD_MODULES(state, modules) {
			state.modules = modules
		},

		ADD_ONTOLOGY_TREE(state, { ontologyTree, moduleId }) {
			if (state.ontologies === undefined) {
				state.ontologies = {}
			}
			if (!state.ontologies[moduleId]) {
				set(state.ontologies, moduleId, ontologyTree)
			}
		},

		ADD_CHECKED_ITEM(state, id) {
			if (!state.checkedItems.includes(id)) {
				state.checkedItems.push(id)
			}
		},

		REMOVE_CHECKED_ITEM(state, id) {
			if (state.checkedItems.includes(id)) {
				state.checkedItems = state.checkedItems.filter(item => item !== id)
			}
		},

		CLEAR_CHECKED_ITEMS(state) {
			state.checkedItems = [] // Clear the checked items
		},

		ADD_SELECTED_ITEM(state, { key, item }) {
			if (!state.selectedItems[key]) {
				set(state.selectedItems, key, item) // Use Vue.set to ensure reactivity
			}
		},

		REMOVE_SELECTED_ITEM(state, key) {
			if (state.selectedItems[key]) {
				Vue.delete(state.selectedItems, key) // Use Vue.delete to ensure reactivity
			}
		},

		CLEAR_SELECTED_ITEMS(state) {
			state.selectedItems = {} // Clear the checked items
		},

		UPDATE_FILTER_OPTIONS(state, { section, index, newFilterOptions }) {
			set(state.selectedCharacteristics[section].characteristics, index, ...newFilterOptions)
		},

		ADD_SELECTED_CHARACTERISTICS(state, { section, characteristics, logic }) {
			if (characteristics && characteristics.length > 0 && section !== '') {
				state.selectedCharacteristics[section].characteristics = [...state.selectedCharacteristics[section].characteristics, ...characteristics]
				state.selectedCharacteristics[section].logic = [...state.selectedCharacteristics[section].logic, ...logic]
			}
		},

		GET_UPLOADED_CHARACTERISTICS(state, characteristics) {
			if (characteristics.inclusionCriteria || characteristics.exclusionCriteria) {
				state.selectedCharacteristics = { ...characteristics }
			}
		},

		CLEAR_SELECTED_CHARACTERISTICS(state) {
			state.selectedCharacteristics = {
				inclusionCriteria: {
					characteristics: [],
					logic: [],
				},
				exclusionCriteria: {
					characteristics: [],
					logic: [],
				},
			}
		},

		UPDATE_CHARACTERISTIC_ORDER(state, { section, value }) {
			if (section !== '') {
				set(state.selectedCharacteristics[section], 'characteristics', value)
			}
		},

		REMOVE_CHARACTERISTICS(state, { index, section }) {
			if (state.selectedCharacteristics && section !== '' && state.selectedCharacteristics[section].characteristics) {
				state.selectedCharacteristics[section].characteristics.splice(index, 1)
			}
		},

		SWITCH_LOGIC(state, { index, section, logic }) {
			if (state.selectedCharacteristics && section !== '' && state.selectedCharacteristics[section].logic) {
				set(state.selectedCharacteristics[section].logic, index, logic)
			}
		},

		REMOVE_LOGIC(state, { index, section }) {
			if (state.selectedCharacteristics && section !== '' && state.selectedCharacteristics[section].logic) {
				state.selectedCharacteristics[section].logic.splice(index, 1)
			}
		},
	},

	actions: {
		addModules({ commit }, modules) {
			commit('ADD_MODULES', modules)
		},

		addOntologyTree({ commit }, payload) {
			if (payload.ontologyTree && payload.moduleId) {
				commit('ADD_ONTOLOGY_TREE', payload)
			} else {
				console.error('Ontology tree or module name is missing')
			}

		},

		addCheckedItem({ commit }, id) {
			commit('ADD_CHECKED_ITEM', id)
		},

		removeCheckedItem({ commit }, id) {
			commit('REMOVE_CHECKED_ITEM', id)
		},

		clearCheckedItems({ commit }) {
			commit('CLEAR_CHECKED_ITEMS')
		},

		addSelectedItem({ commit }, payload) {
			commit('ADD_SELECTED_ITEM', payload)
		},

		removeSelectedItem({ commit }, key) {
			commit('REMOVE_SELECTED_ITEM', key)
		},

		clearSelectedItems({ commit }) {
			commit('CLEAR_SELECTED_ITEMS')
		},

		updateFilterOptions({ commit }, payload) {
			commit('UPDATE_FILTER_OPTIONS', payload)
		},

		addSelectedCharacteristics({ commit }, payload) {
			commit('ADD_SELECTED_CHARACTERISTICS', payload)
		},

		getUploadCharacteristics({ commit }, characteristics) {
			commit('GET_UPLOADED_CHARACTERISTICS', characteristics)
		},

		clearSelectedCharacteristics({ commit }) {
			commit('CLEAR_SELECTED_CHARACTERISTICS')
		},

		updateCharacteristicOrder({ commit }, payload) {
			commit('UPDATE_CHARACTERISTIC_ORDER', payload)
		},

		removeCharacteristics({ commit }, payload) {
			commit('REMOVE_CHARACTERISTICS', payload)
		},

		switchLogic({ commit }, payload) {
			commit('SWITCH_LOGIC', payload)
		},

		removeLogic({ commit }, payload) {
			commit('REMOVE_LOGIC', payload)
		},
	},

	getters: {
		getCheckedItem: (state) => (id) => {
			return state.checkedItems.includes(id)
		},

		getSelectedItem: (state) => (id) => {
			return state.selectedItems[id]
		},

		getEditCharacteristic: (state) => (id, index, criteriaType) => {
			const characteristic = state.selectedCharacteristics[criteriaType].characteristics[index]
			if (characteristic.id === id) {
				return characteristic
			}
		},

		getOntologyTree: (state) => (moduleName) => {
			return state.ontologies && moduleName ? state.ontologies[moduleName] : null
		},
	},
})
