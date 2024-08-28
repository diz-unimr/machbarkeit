/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRange } from '../types/TimeRangeOptionsData.ts'

export interface OntologyTreeElement {
    children: Array<OntologyTreeElement> | undefined;
    id: string;
    display: string;
    context: {
        code: string,
        display: string,
        system: string,
        version: string,
    };
    leaf: boolean | undefined;
    selectable: boolean | undefined;
    termCodes: [
        {
            code: string,
            display: string,
            system: string,
        }
    ] | undefined;
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
}

/* interface CriteriaData extends OntologyTreeElement {
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
} */

export interface OntologySearchTreeModalData {
	activeTab: number | string;
	ontologyResponse: OntologyTreeElement[] | null;
    /* criteriaData: CriteriaData[] | null; */
	selectedItems: OntologyTreeElement[];
    isSearchResultNoData: Array<boolean>;
}
