/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

export interface TimeRangeType {
    timeRestriction: {
        beforeDate?: string;
        afterDate?: string;
        atDate?: string;
    };
    isFilterComplete?: boolean;
}

export interface TimeRangeFilter {
    type: string;
    beforeDate: string;
    afterDate: string;
    atDate: string;
}
export interface TimeRangeOptionsData {
    isDateInputValid: boolean;
    isFilterComplete: boolean;
    selectedValue: TimeRangeFilter
}
