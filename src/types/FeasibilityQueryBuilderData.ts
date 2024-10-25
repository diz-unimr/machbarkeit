/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { OntologyTreeElement } from './OntologySearchTreeModalData.ts'

interface ValueDefinition {
    allowedUnits: [
        {
            code: string;
            display: string;
            system: string;
            version: string | null | undefined;
        }
    ];
    max: number | null;
    min: number | null;
    optional: boolean;
    precision: number;
    referenceCriteriaSet: null;
    selectableConcepts: [
        {
            code: string;
            display: string;
            system: string;
            version: string | null | undefined;
        }
    ];
    type: string;
}

export interface Profile {
    attributeDefinitions: Array<object>;
    name: string;
    timeRestrictionAllowed: boolean;
    valueDefinition: ValueDefinition | null;
}

export interface FeasibilityQueryBuilderData {
    searchInputText: string;
    inclusionSearchInputText: string;
    exclusionSearchInputText: string;
    inclusionSearchInput: string;
    exclusionSearchInput: string;
    isLimitationsCriteriaOpen: boolean;
    isOntologySearchTreeOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: OntologyTreeElement[] | null;
    selectedEditedCriteriaIndex: number | null;
    selectedInclusionCharacteristics: OntologyTreeElement[];
    selectedExclusionCharacteristics: Array<OntologyTreeElement>;
    isStateEditFilter: boolean;
    debouncedHandler: _.DebouncedFunc<() => void> | null;
    imgDelete: string;
}
