/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
export interface QuantityTypeData {
    typeSymbol: {
        'kein Filter': string,
        gleich: string,
        kleiner: string,
        größer: string,
        zwischen: string,
    },
    comparisonRestriction: {
        type: string,
        typeSymbol: string,
        unit: string | undefined,
        value: string,
        min: string,
        max: string,
    },
    isFilterOptional: boolean |undefined,
}
