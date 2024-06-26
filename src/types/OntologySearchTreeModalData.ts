/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType, QuantityType, TimeRange } from './LimitationsSelectedCriteriaModalData.ts'

export interface OntologyTreeElement {
    children: Array<OntologyTreeElement> | undefined;
    context: {
        code: string,
        display: string,
        system: string,
        version: string,
    };
    display: string;
    id: string;
    leaf: boolean;
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
    selectable: boolean;
    termCodes: [
        {
            code: string,
            display: string,
            system: string,
        }
    ];
}

export interface CriteriaData extends OntologyTreeElement {
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
}

export interface SubmittedItems {
    criteriaType: string;
    items: OntologyTreeElement;
}

export interface OntologySearchTreeModalData {
	activeTab: number | string;
	criteriaResponse: OntologyTreeElement[] | null;
    criteriaData: CriteriaData[] | null;
	selectedItems: OntologyTreeElement[];
}
