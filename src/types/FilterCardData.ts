/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

/* export interface FilterTypeData {
	status: string;
	type: string;
	display: string;
	value: ConceptTypeData['selectedConcepts'] | QuantityTypeData['comparisonRestriction'] | TimeRangOptaionData['timeRangeRestriction'];
	isFilterOptional: boolean;
	isFilterComplete: boolean;
} */

export interface FilterCardData {
	state: boolean,
	isResetDisabled: boolean,
	imgExpand: string
}
