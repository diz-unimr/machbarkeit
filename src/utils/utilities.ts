/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import lodash from 'lodash'
import store from '../store/store'
import type { Criterion, Module } from '../types/OntologyPanelData'
/**
 * Returns the theme color associated with a given module name.
 * @param {string} moduleName - The name of the module.
 * @return {string} The hex color code for the module theme.
 */
export function getTabColor(moduleName: string): string {
	if (moduleName === 'Person') {
		return '#3498DB'
	} else if (moduleName === 'Diagnose') {
		return '#9B59B6'
	} else if (moduleName === 'Prozedur') {
		return '#FBB016'
	} else if (moduleName === 'Laboruntersuchung') {
		return '#1FC48B'
	} else return 'default'
}

/**
 * Recursively transforms the keys of each object in the data array to camelCase.
 * @param data The array of objects whose keys should be transformed to camelCase.
 */
export function transformObjectKeys<T extends { children?: T[] }>(data: T[]): T[] {
	const response: T[] = data.map((obj: T) => {
		const transformedObj = lodash.mapKeys(obj, (value, key) => lodash.camelCase(key)) as unknown as T
		if ('children' in transformedObj && transformedObj.children!.length > 0) {
			transformedObj.children = transformObjectKeys(transformedObj.children!) as T[]
		}
		return transformedObj
	})
	return response
}

/**
 * Sets the context for a Criterion based on the matching module from this.modules.
 * @param criterion - The Criterion to update with context fields.
 */
export function setCriterionContext(criterion: Criterion) {
	const modules = store.state.modules as Module[]
	if (modules) {
		const module = modules.find((module: Module) => module.id === criterion.moduleId)
		criterion.context = {
			code: module!.fdpgCdsCode || '',
			display: module!.name || '',
			system: module!.fdpgCdsSystem || '',
			version: module!.version || '',
		}
		return criterion
	}
}
