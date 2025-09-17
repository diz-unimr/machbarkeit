/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

import axios, { AxiosError, type AxiosResponse } from 'axios'
import type { Criterion, Module } from '../types/OntologyPanelData'
import { transformObjectKeys } from '../utils/utilities'

let abortController: AbortController | null = null
/**
 * Aborts the previous ontology API request if one is in progress and resets the abort controller.
 */
export async function setAbortController(): Promise<void> {
	// if there is an existing abortController, abort the previous request
	if (abortController) {
		abortController.abort()
		abortController = null
		await Promise.resolve()
	}
}

/**
 * Fetches ontology data for a given module from the MDR API.
 * @param module The module for which to fetch ontology data.
 * @param searchText Optional search text to filter ontology concepts.
 * @return {Promise<[Criterion[] | null, string]>} A promise that resolves to a tuple containing the ontology data or null, and a request warning string.
 */
export async function getOntology(module: Module, searchText: string = ''): Promise<[Criterion[] | null, string]> {
	// Creates a new AbortController for the ontology API request.
	abortController = new AbortController()
	let apiResponse: AxiosResponse
	try {
		if (searchText.length > 0) {
			apiResponse = await axios.post('https://machbarkeit.diz.uni-marburg.de/mdr/ontology/concepts/search',
				{
					module_id: module.id,
					search_term: searchText,
					display: 'tree',
				},
				{
					signal: abortController.signal, // Attach the cancel token to the request
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
		} else {
			apiResponse = await axios.get('https://machbarkeit.diz.uni-marburg.de/mdr/ontology/tree/' + module.id,
				{ signal: abortController.signal })
		}
		// Convert object keys to camelCase using lodash
		const response = transformObjectKeys(apiResponse.data) as Criterion[]
		const requestWarning = getRequestWarning(apiResponse.status as number)
		return [response, requestWarning]
	} catch (error) {
		if ((error as AxiosError).name === 'CanceledError' || (error as AxiosError).message === 'canceled') {
			return [null, (error as AxiosError).message] // Re-fetch the ontology data if the request was canceled
		}
		if ((error as AxiosError).code === 'ERR_NETWORK') {
			alert('Network Error')
		}
		const requestWarning = getRequestWarning((error as AxiosError).status as number, (error as AxiosError).message)
		return [null, requestWarning] // Return error status and null data
	}
}

/**
 * Fetches a single ontology concept by its ID.
 * @param id The unique identifier of the ontology concept to fetch.
 */
export async function getConcept(id: string): Promise<Criterion | null> {
	let response: AxiosResponse
	try {
		response = await axios.get('https://machbarkeit.diz.uni-marburg.de/mdr/ontology/concepts/' + id)
		const conceptResponse = transformObjectKeys([response.data]) as Criterion[]
		return conceptResponse[0]
	} catch (error) {
		if ((error as AxiosError).code === 'ERR_NETWORK') {
			alert('Network Error')
		}
		return null
	}
}

/**
 * @param status The HTTP status code of the response.
 * @param message Optional error message in case of an error.
 */
function getRequestWarning(status: number, message?: string): string {
	let requestWarning: string = ''
	if (status === 200) {
		requestWarning = ''
	} else if (status === 400) {
		requestWarning = 'Bitte mindestens 2 Buchstaben eingeben'
	} else {
		requestWarning = ''
		alert(message)
	}
	return requestWarning
}
