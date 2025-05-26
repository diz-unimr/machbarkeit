/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRangeType } from '../types/TimeRangeOptionsData.ts'
import type { Criterion, Module } from './OntologySearchTreeModalData.ts'

export interface SelectedCharacteristics {
    characteristics: Array<Criterion>;
    logic: Array<string>;
}

export type QueryCriterionData = {
    id: Criterion['id'];
    termCodes: Criterion['termCodes'];
    context: Criterion['context'];
} & (ConceptType | QuantityType | TimeRangeType | undefined)

export interface FeasibilityQueryBuilderData {
    modules: Array<Module> | null;
    searchInputText: string;
    inclusionSearchInputText: string;
    exclusionSearchInputText: string;
    isLimitationsCriteriaOpen: boolean;
    isOntologySearchTreeOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: Criterion[] | null;
    selectedEditedCriteriaIndex: number | null;
    selectedInclusionCharacteristics: SelectedCharacteristics;
    selectedExclusionCharacteristics: SelectedCharacteristics;
    queryData: {
        version: string;
        display: string;
        inclusionCriteria?: Array<QueryCriterionData>[];
		exclusionCriteria?: Array<QueryCriterionData>[];
    };
    isStateEditFilter: boolean;
    searchInputWarning: string;
    imgDelete: string;
}
