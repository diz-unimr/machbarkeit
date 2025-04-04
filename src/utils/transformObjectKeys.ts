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