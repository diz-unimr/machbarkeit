/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

import axios, { AxiosError, type AxiosResponse } from 'axios'
import type { FeasibilityQueryContainerData } from '../types/FeasibilityQueryContainerData'

/**
 * Executes a feasibility query with the provided data.
 * @param {FeasibilityQueryContainerData['queryData']} data - The query data to send in the request.
 * @param {AbortController} abortController - The AbortController to cancel the request if needed.
 * @return {Promise<[number | null, string | null]>} The response data from the API.
 */

/**
 *
 * @param data
 * @param abortController
 */
export async function getMachbarkeit(data: FeasibilityQueryContainerData['queryData'], abortController: AbortController): Promise<[number | null, string | null]> {
	let numberOfPatients: number | null = null
	let errorMessage: string | null = null
	try {
		const response = await axios.post(process.env.BACKEND_API_BASE + '/feasibility/request',
			JSON.stringify(data),
			{
				headers: {
					'Content-Type': 'application/json',
				},
				signal: abortController.signal,
				withCredentials: true,
			},
		)

		// check if accepted
		if (response.status === 202) {
			// check location header
			const poll = response.headers.location

			// numberOfPatients =
			numberOfPatients = await new Promise((resolve, reject) => {
				const intervalId = setInterval(async () => {
					try {
						// poll result url
						const r: AxiosResponse = await axios.get(poll, {
							signal: abortController.signal,
							withCredentials: true,
							validateStatus(status) {
								return status < 400 || status === 404
							},
						})

						// 404 => not yet ready
						if (r.status === 200) {

							clearInterval(intervalId)

							// result: parse response
							if (r && String(r.data)) {
								resolve(r.data)
							}
						}
					} catch (error) {
						clearInterval(intervalId)
						reject(error)
					}
				}, 1000)
			})
		}
	} catch (error) {
		if ((error as AxiosError).name === 'CanceledError' || (error as AxiosError).message === 'canceled') {
			console.log('Request was canceled by user')
		} else if ((error as AxiosError).response) {
			errorMessage = ((error as AxiosError).response!.data as { error: string }).error
			/* const errorText = 'None of the following contextual term codes'
            if (errorMessage.startsWith(errorText)) {
                const labCodeError = [...errorMessage.matchAll(/code=([^,\]]+)/g)].map(m => m[1]).filter(item => item !== 'Laboruntersuchung')
                const labCodeErrorAlert = labCodeError.length > 1
                    ? labCodeError.slice(0, -1).join(', ') + ' und ' + labCodeError[labCodeError.length - 1]
                    : labCodeError[0] || ''
                const textAlert = labCodeError.length === 1 ? ' wurde im Codebaum nicht gefunden.' : ' wurden im Codebaum nicht gefunden.'
                alert(labCodeErrorAlert + textAlert + ' Bitte kontaktieren Sie den DIZ-Support.')
            } */
			errorMessage = error ? 'Found some error!' : null
		}
	}
	return [numberOfPatients, errorMessage]

}
