/*
    SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
    SPDX-License-Identifier: AGPL-3.0-or-later
*/

import axios, { AxiosError, type AxiosResponse } from 'axios'
import lodash from 'lodash'
import type { Module } from '../types/OntologyPanelData'
import { getTabColor } from '../utils/utilities'

/**
 * Fetches ontology concepts from the MDR API.
 * @return {Promise<AxiosResponse<string>>} The API response as a string (currently empty).
 */
export async function getModules(): Promise<Module[] | null> {
	try {
		const apiResponse: AxiosResponse = (await axios.get('https://machbarkeit.diz.uni-marburg.de/mdr/ontology/modules'))
		const response: Module[] = apiResponse.data.map((module: Module) => {
			const modules = lodash.mapKeys(module, (value, key) => lodash.camelCase(key))
			modules.color = getTabColor(modules.name)
			return modules
		})
		return response
	} catch (error) {
		// eslint-disable-next-line no-console
		console.log((error as AxiosError).message)
		alert((error as AxiosError).message)
		return null
	}
}
