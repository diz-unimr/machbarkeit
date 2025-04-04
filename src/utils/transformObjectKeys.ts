/* SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
SPDX-License-Identifier: AGPL-3.0-or-later */

import type { Criterion, Modules } from '../types/OntologySearchTreeModalData' // 'types/OntologySearchTreeModalData.ts'
import lodash from 'lodash'

function transformObjectKeys(data: any): any {
    const response: Criterion[]  = data.map((obj: Criterion | Modules) => {
        let transformedObj = lodash.mapKeys(obj, (value, key) => lodash.camelCase(key)) as unknown as Criterion
            if (transformedObj.children && transformedObj.children.length > 0) {
                transformedObj.children = transformObjectKeys(transformedObj.children)
            }
            return transformedObj
        })
        return response
    }
export default transformObjectKeys