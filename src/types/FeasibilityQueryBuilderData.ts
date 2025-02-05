/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { QueryCriterionData } from '../components/FeasibilityQueryDisplay.vue'
import type { Criterion, Modules } from './OntologySearchTreeModalData.ts'

export interface SelectedCharacteristics {
    characteristics: Array<Criterion>;
    logic: Array<string>;
}

export interface FeasibilityQueryBuilderData {
    modules: Array<Modules> | null;
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
    debouncedHandler: _.DebouncedFunc<() => void> | null;
    imgDelete: string;
}
