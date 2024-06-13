export interface ConceptType {
    completeFilter: boolean;
    isFilterOptional: boolean;
    type: string;
    value: Array<string>;
}

export interface QuantityType {
    completeFilter: boolean;
    isFilterOptional: boolean;
    type: string;
    value: {
        max: string;
        min: string;
        type: string;
        typeSymbol: string;
        unit: string;
        value: string;
    };
}

export interface TimeRange {
    completeFilter: boolean;
    isFilterOptional: boolean;
    type: string;
    value: {
        fromDate: string;
        fromDateFormatted: string;
        toDate: string;
        toDateFormatted: string;
        type: string;
    };
}

/* interface ValueDefinition {
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
} */

export interface LimitationsSelectedFeaturesData {
    filterInfo: Array<{
        display: string;
        conceptType: ConceptType | undefined;
        quantityType: QuantityType | undefined;
        timeRange: TimeRange | undefined;
    }>,
    notEmptyProfileName: Array<object>;
    // uiProfile: UiProfile | null;
    isFilterComplete: boolean;
}
