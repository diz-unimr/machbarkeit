/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

import type { ConceptType, QuantityType, TimeRange } from '../types/LimitationsSelectedFeaturesData.ts'
import type { FilteredCriteriaData } from '../components/CriteriaNestedTreeNode.vue'

export interface CriteriaResponse {
    children: Array<CriteriaResponse> | undefined;
    context: object;
    display: string;
    id: string;
    leaf: boolean;
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
    selectable: boolean;
    termCodes: Array<object>;
}

export interface CriteriaData extends CriteriaResponse {
    conceptType: ConceptType | undefined;
	timeRange: TimeRange | undefined;
	quantityType: QuantityType | undefined;
}

export interface SubmittedItems {
    criteriaType: string;
    items: CriteriaResponse;
}

export interface SearchTreeOverlayContentData {
	activeTab: number | string;
	criteriaResponse: CriteriaResponse[] | null;
    criteriaData: CriteriaData[] | null;
	selectedItems: CriteriaResponse[];
    tempArray: Array<FilteredCriteriaData>;
}
