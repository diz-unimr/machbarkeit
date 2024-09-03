/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRange } from '../types/TimeRangeOptionsData.ts'
import type { Profile } from './FeasibilityQueryBuilderData.ts'

export interface FilterInfo {
    id: string;
    display: string;
    context: {
        code: string,
        display: string,
        system: string,
        version: string,
    };
    conceptType: ConceptType | null;
    quantityType: QuantityType | null;
    timeRange: TimeRange | null;
}

export interface LimitationsSelectedCriteriaCardData {
    profile: Profile | null,
    filterInfo: FilterInfo | null,
    imgDelete: string,
}
