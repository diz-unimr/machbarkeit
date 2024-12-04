/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData.ts'

export interface FeasibilityQueryBuilderData {
    searchInputText: string;
    inclusionSearchInputText: string;
    exclusionSearchInputText: string;
    inclusionSearchInput: string;
    exclusionSearchInput: string;
    isLimitationsCriteriaOpen: boolean;
    isOntologySearchTreeOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: Criterion[] | null;
    selectedEditedCriteriaIndex: number | null;
    selectedInclusionCharacteristics: Array<Criterion>;
    selectedExclusionCharacteristics: Array<Criterion>;
    isStateEditFilter: boolean;
    debouncedHandler: _.DebouncedFunc<() => void> | null;
    imgDelete: string;
}
