/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData'

export interface QuantityType {
    valueFilter: {
        /* access only object inside array */
        unit: NonNullable<Criterion['filterOptions']>[number];
        comparator?: string;
        value?: number;
        minValue?: number;
        maxValue?: number;
        type: string;
    };
}

export interface QuantityOptionsData {
    newSelectedCriterion: Criterion;
    isFilterComplete: boolean;
    selectedValue: {
        unit: NonNullable<Criterion['filterOptions']>[number];
        comparator: string;
        value: number;
        minValue: number;
        maxValue: number;
        type: string;
    }
    quantityType?: QuantityType;
}
