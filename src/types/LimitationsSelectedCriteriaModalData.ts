/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from '../types/ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData'
import type { TimeRange } from '../types/TimeRangeOptionsData'

export interface LimitationsSelectedCriteriaModalData {
    selectedCriteriaFiltersInfo: Array<ConceptType | QuantityType | TimeRange>;
    isFilterComplete: boolean;
}
