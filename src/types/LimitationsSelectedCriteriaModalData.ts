/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRange } from '../types/TimeRangeOptionsData'

export interface FilterOptions {
    id: number;
    filterName: string;
    type: string;
    optional: boolean;
    filterOptions: {
        code: string;
        display: string;
        system: string;
        version: string;
    }[];
}

export interface LimitationsSelectedCriteriaModalData {
	filterOptions: FilterOptions[] | null;
    selectedCriteriaFiltersInfo: Array<ConceptType | QuantityType | TimeRange>;
    isFilterComplete: boolean;
}
