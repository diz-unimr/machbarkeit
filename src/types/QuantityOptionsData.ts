/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

export interface QuantityType {
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

export interface QuantityOptionsData {
    typeSymbol: {
        'kein Filter': string,
        gleich: string,
        kleiner: string,
        größer: string,
        zwischen: string,
    },
    comparisonRestriction: QuantityType['value']
}
