/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
export interface TimeRangOptaionData {
    timeRangeRestriction: {
        type: string,
        fromDate: string | null,
        fromDateFormatted: string | null,
        toDate: string | null,
        toDateFormatted: string | null,
    },
    isFilterOptional: boolean
}