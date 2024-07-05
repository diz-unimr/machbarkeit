/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

export interface ConceptType {
	type: string;
    display: string;
    isFilterOptional: boolean;
    isFilterComplete: boolean;
    value: Array<string>;
}

export interface ConceptOptionsData {
	selectedConcepts: ConceptType['value'];
}
