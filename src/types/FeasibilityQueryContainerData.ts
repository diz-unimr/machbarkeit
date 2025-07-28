/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData'
import type { QuantityType } from './QuantityOptionsData'
import type { TimeRangeType } from './TimeRangeOptionsData'
import type { Criterion } from './OntologyPanelData'

export interface CharacteristicGroup {
  characteristics: Criterion[];
  logic: string[];
}

export interface SelectedCharacteristics {
	inclusionCriteria: CharacteristicGroup,
	exclusionCriteria: CharacteristicGroup,
}

export type QueryCriterion = {
	id: Criterion['id'];
	termCodes: Criterion['termCodes'];
	context: Criterion['context'];
} & Partial<ConceptType | QuantityType | TimeRangeType>;

export interface MachbarkeitQueryData {
	display?: string;
	version?: string;
	inclusionCriteria?: QueryCriterion[][] | [];
	exclusionCriteria?: QueryCriterion[][] | [];
}

export interface FeasibilityQueryContainerData {
	queryData: MachbarkeitQueryData | null;
	numberOfPatients: number | null;
	isSaveModalOpen: boolean;
	errorMessage: string | null;
	isQueryRunning: boolean;
	abortController: AbortController | null,
	hasDataFromUpload: boolean;
}
