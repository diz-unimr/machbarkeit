/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData'

export interface TimeRangeType {
    timeRestriction: {
        beforeDate?: string;
        afterDate?: string;
    };

}

export interface TimeRangeOptionsData {
    newSelectedCriterion: Criterion;
    isFilterComplete: boolean;
    selectedValue: {
        type: string;
        beforeDate: string;
        afterDate: string;
        atDate: string;
    }
    timeRangeType?: TimeRangeType;
}
