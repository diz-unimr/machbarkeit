/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { FilterInfo } from './FeasibilityQueryBuilderData.ts'

interface OptionType {
    completeFilter: boolean;
    isFilterOptional: boolean;
    type: string;
}

export interface ConceptType extends OptionType {
    value: Array<string>;
}

export interface QuantityType extends OptionType {
    value: {
        max: string;
        min: string;
        type: string;
        typeSymbol: string;
        unit: string;
        value: string;
    };
}

export interface TimeRange extends OptionType {
    value: {
        fromDate: string;
        fromDateFormatted: string;
        toDate: string;
        toDateFormatted: string;
        type: string;
    };
}

interface ValueDefinition {
    allowedUnits: Array<object>;
    max: number | null;
    min: number | null;
    optional: boolean;
    precision: number;
    referenceCriteriaSet: null;
    selectableConcepts: Array<object>;
    type: string;
}

interface UiProfileDetails {
    attributeDefinitions: Array<object>;
    name: string;
    timeRestrictionAllowed: boolean;
    valueDefinition: ValueDefinition | null;
}

export interface UiProfile {
    Diagnose: UiProfileDetails;
    Prozedur: UiProfileDetails;
    'Gegenwärtiges chronologisches Alter': UiProfileDetails;
    Geschlecht: UiProfileDetails;
}

export interface LimitationsSelectedCriteriaModalData {
    filterInfo: FilterInfo[],
    notEmptyProfileName: Array<object>;
    isFilterComplete: boolean;
}
