/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { FilterInfo } from './LimitationsSelectedCriteriaCardData.ts'

export interface LimitationsSelectedCriteriaModalData {
    filterInfo: FilterInfo[],
    selectedCriteriaFiltersInfo: FilterInfo[];
    isFilterComplete: boolean;
}
