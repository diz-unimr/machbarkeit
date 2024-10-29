/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRange } from '../types/TimeRangeOptionsData.ts'

export interface OntologyTreeElement {
    children: Array<OntologyTreeElement> | undefined;
    id: number;
    moduleId: number;
    parentId: number | null;
    filterOptionsId: number | undefined;
    display: string;
    termCodes: {
        code: string,
        display: string,
        system: string,
    };
    filterOption: object | undefined;
    timeRestrictionAllowed: boolean | undefined;
    typeQuantity: boolean | undefined;
    selectable: boolean;
    leaf: boolean;
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
}

export interface Modules {
    id: number;
    display: string;
    version: string;
}

export interface OntologyTree {
    display: string;
    id: number;
    leaf: boolean;
    moduleId: number;
    parentId: number | null;
    selectable: boolean;
}

export interface OntologySearchTreeModalData {
	activeTab: number;
    wasTabClicked: Array<boolean>;
	ontologyResponse: OntologyTreeElement[] | null;
	selectedItems: OntologyTreeElement[];
    isSearchResultNoData: Array<boolean>;
    modules: Modules[] | null;
    ontologyTree: OntologyTreeElement[] | null;
    ontologyTreeSearch: Array<OntologyTreeElement | null>;
    hashId: Array<string>;
}
