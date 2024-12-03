/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData'

export interface QuantityType2 {
    type: string;
    display: string;
    isFilterOptional: boolean;
    isFilterComplete: boolean;
    value: {
        type: string,
        typeSymbol: string,
        unit: string | undefined,
        value: string,
        min: string,
        max: string,
    },
}

export interface QuantityType {
    valueFilter: {
        /* access only object inside array */
        unit: Criterion['filterOptions'][number];
        comparator?: string;
        value?: number;
        minValue?: number;
        maxValue?: number;
        type: string;
    };
}

export interface QuantityOptionsData {
    isFilterComplete: boolean;
    selectedValue: {
        unit: Criterion['filterOptions'][number];
        comparator: string;
        value: number;
        minValue: number;
        maxValue: number;
        type: string;
    }
    quantityType?: QuantityType;
}
