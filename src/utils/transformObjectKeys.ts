/* SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
SPDX-License-Identifier: AGPL-3.0-or-later */

import type { Criterion, Module } from '../types/OntologySearchTreeModalData' // 'types/OntologySearchTreeModalData.ts'
import lodash from 'lodash'

/**
 * Transforms the keys of each object in the data array to camelCase recursively.
 * @param data Array of Criterion objects to be transformed.
 */

/**
 *
 * @param data
 */
function transformObjectKeys(data: Criterion[]): Criterion[] {
	const response: Criterion[] = data.map((obj: Criterion | Module) => {
		const transformedObj = lodash.mapKeys(obj, (value, key) => lodash.camelCase(key)) as unknown as Criterion
		if (transformedObj.children && transformedObj.children.length > 0) {
			transformedObj.children = transformObjectKeys(transformedObj.children)
		}
		return transformedObj
	})
	return response
}
export default transformObjectKeys
