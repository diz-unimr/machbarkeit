/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { Criterion } from './OntologySearchTreeModalData'

export interface ConceptType {
    termCodes: Array<Criterion['termCodes']>;
    context: Criterion['context'];
    valueFilter?: {
        selectedConcepts: Criterion['filterOptions'];
        type: string;
    };
}

export interface ConceptOptionsData {
    selectedValue: Criterion['filterOptions'] | [];
    conceptType: ConceptType;
}
