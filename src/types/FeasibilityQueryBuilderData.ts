/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { OntologyTreeElement } from './OntologySearchTreeModalData.ts'
import type { ConceptType, QuantityType, TimeRange } from './LimitationsSelectedCriteriaModalData.ts'

export interface SelectedCriteriaData {
	criteriaType: string;
	selectedItems: OntologyTreeElement[];
}

export interface FilterInfo {
    id: string;
    display: string;
    conceptType: ConceptType | undefined;
    quantityType: QuantityType | undefined;
    timeRange: TimeRange | undefined;
}

export interface ValueDefinition {
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
            version: string;
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
    einschlussTextSerach: string;
    ausschlussTextSerach: string;
    isCriteriaOptionOpen: boolean;
    isCriteriaOptionEditorOpen: boolean;
    isCriteriaContentOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: OntologyTreeElement[] | null;
    selectedEditedCriteria: OntologyTreeElement[] | null;
    selectedEditedCriteriaIndex: number | null
    selectedCharacteristicsEin: Array<FilterInfo>;
    selectedCharacteristicsAus: Array<FilterInfo>;
    btnLastChild: number | null;
    imgDelete: string;
}
