import type { CriteriaResponse } from '../types/SearchTreeOverlayContentData.ts'
import type { ConceptType, QuantityType, TimeRange } from '../types/LimitationsSelectedFeaturesData.ts'

export interface SelectedCriteriaData {
	criteriaType: string;
	selectedItems: CriteriaResponse[];
}

export interface FilterInfo {
    display: string;
    conceptType: ConceptType;
    quantityType: QuantityType;
    timeRange: TimeRange;
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

export interface FeasibilityQueryBuilderData {
    // criteriaResponse: CriteriaResponse[] | null,
    uiProfile: UiProfile | null;
    einschlussTextSerach: string;
    ausschlussTextSerach: string;
    isCriteriaOptionOpen: boolean;
    isCriteriaOverlayOpen: boolean;
    isEinschlusskriterienOverlayOpen: boolean;
    isAusschlusskriterienOverlayOpen: boolean;
    criteriaOverlayType: string;
    selectedCriteria: CriteriaResponse[] | null;
    selectedCharacteristics: Array<FilterInfo>;
    imgDelete: string;
}
