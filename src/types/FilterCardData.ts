/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptTypeData } from './ConceoptTypeData.ts';
import type { QuantityTypeData } from '../types/QuantityTypeData.ts'
import type { TimeRangOptaionData } from './TimeRangeOptionData.ts';

export interface SelectedOptionData {
	type: string;
	value: ConceptTypeData['selectedConcepts'] | QuantityTypeData['comparisonRestriction'] | TimeRangOptaionData['timeRangeRestriction'];
	isFilterOptional: boolean;
	completeFilter: boolean;
}

export interface FilterCardData {
	state: boolean,
	isResetDisabled: boolean,
	groupFilter: Array<SelectedOptionData>,
	imgExpand: string
}
