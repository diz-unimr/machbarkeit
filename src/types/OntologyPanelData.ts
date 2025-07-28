/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData'
import type { QuantityType } from './QuantityOptionsData'
import type { TimeRangeType } from './TimeRangeOptionsData'

export interface Criterion {
    children: Criterion[];
    id: string;
    moduleId: string;
    parentId: string | null;
    display: string;
    termCodes: {
        code: string;
        system: string;
        display: string;
        version: string;
    }[];
    context?: {
        code: string;
        system: string;
        version: string;
        display: string;
    };
    selectable: boolean;
    leaf: boolean;
    timeRestrictionAllowed?: boolean | null;
    filterName?: string;
    filterType: string | null;
    filterOptions: {
        code: string;
        display: string;
        system?: string;
        version?: string;
    }[] | null;
    valueFilter?: ConceptType['valueFilter'] | QuantityType['valueFilter'];
    timeRestriction?: TimeRangeType['timeRestriction'];
    filterCompleteStatus?: boolean;
    color?: string;
}

export interface Module {
    id: string;
    name: string;
    fdpgCdsCode: string;
    fdpgCdsSystem: string;
    fdpgCdsVersion: string;
    version: string;
    color: string;
}

export interface OntologyPanelData {
    modules?: Module[] | null;
    ontologyTree?: Criterion[] | null;
    isLoading: boolean;
    activeModule?: Module;
	nextModule?: Module;
	selectedItems: Criterion[];
    checkedItems?: Criterion[];
    isWarningModalOpened: boolean;
    currentTheme: string;
}
