/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
export interface TimeRange {
    type: string;
    display: string;
    isFilterOptional: boolean;
    isFilterComplete: boolean;
    value: {
        type: string,
        fromDate: string | null,
        fromDateFormatted: string | null,
        toDate: string | null,
        toDateFormatted: string | null,
    };
}

export interface TimeRangeOptionsData {
    timeRangeRestriction: TimeRange['value']
}
