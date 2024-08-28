/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { OntologyTreeElement } from './OntologySearchTreeModalData.ts'
import type { FilterInfo } from './LimitationsSelectedCriteriaCardData.ts'

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

export interface UiProfile {
    Diagnose: Profile;
    Prozedur: Profile;
    'Gegenwärtiges chronologisches Alter': Profile;
    Geschlecht: Profile;
}

export interface FeasibilityQueryBuilderData {
    uiProfile: UiProfile | null;
    inclusionSearchInputTemp: string;
    exclusionSearchInputTemp: string;
    inclusionSearchInput: string;
    exclusionSearchInput: string;
    isLimitationsCriteriaOpen: boolean;
    isOntologySearchTreeOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: OntologyTreeElement[] | null;
    selectedEditedCriteria: FilterInfo[] | null;
    selectedEditedCriteriaIndex: number | null
    selectedInclusionCharacteristics: Array<FilterInfo>;
    selectedExclusionCharacteristics: Array<FilterInfo>;
    isStateEditFilter: boolean;
    debouncedHandler: _.DebouncedFunc<() => void> | null;
    imgDelete: string;
}
