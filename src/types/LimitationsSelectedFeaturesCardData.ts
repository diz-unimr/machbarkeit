/* interface timeRestrictionAllowed {
    display: string,
    state: boolean,
    isResetDisabled: boolean
} */

/* interface valueDefinition extends timeRestrictionAllowed {
    selectedConcepts: Array<object> | null,
    selectedTimeRange: Array<object> | null
} */

/* interface profileFilterInfo {
    type: string | null,
    timeRestrictionAllowed: timeRestrictionAllowed,
    valueDefinition: valueDefinition
} */

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

interface profile {
    attributeDefinitions: Array<object>,
    name: string,
    timeRestrictionAllowed: boolean,
    valueDefinition: ValueDefinition
}

export interface LimitationsSelectedFeaturesCardData {
    // timeRestrictionAllowed: timeRestrictionAllowed,
    // valueDefinition: valueDefinition,
    // profileFilterInfo: profileFilterInfo,
    profile: profile | null,
    imgDelete: string,
}
