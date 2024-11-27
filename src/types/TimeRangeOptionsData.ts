/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData'

export interface TimeRange2 {
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

export interface TimeRangeType {
    termCodes: Array<Criterion['termCodes']>;
    context: Criterion['context'];
    timeRestriction?: {
        beforeDate?: string;
        afterDate?: string;
    };
    
}

export interface TimeRangeOptionsData {
    isFilterComplete: boolean;
    selectedValue: {
        type: string;
        beforeDate: string;
        afterDate: string;
        atDate: string;
    }
    timeRangeType: TimeRangeType;
}
