/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

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
    profile: profile | null,
    imgDelete: string,
}
