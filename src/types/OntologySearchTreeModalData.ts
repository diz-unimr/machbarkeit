/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRangeType } from '../types/TimeRangeOptionsData.ts'

export interface Criterion {
    children: Array<Criterion>;
    id: string;
    moduleId: string;
    parentId: string | null;
    display: string;
    termCodes: {
        code: string;
        system: string;
        display: string;
        version: string;
    }[],
    context?: {
        code: string;
        system: string;
        version: string;
        display: string;
    };
    loinc?: string;
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
    selectedFilter?: ConceptType | QuantityType | TimeRangeType;
}

export interface Module {
    id: string;
    name: string;
    fdpgCdsCode: string;
    fdpgCdsSystem: string;
    fdpgCdsVersion: string;
    version: string;
}

export interface OntologySearchTreeModalData {
	activeTab?: string;
    requestStatus?: number;
	selectedItems: Criterion[];
    checkedItems?: Criterion[];
    isSearchResultNoData: Array<boolean>;
    modules: Module[] | null;
    ontologyTree: Criterion[] | null;
    context: {
        code: string;
        system: string;
        version: string;
        display: string;
    } | null;
    isCheckboxChecked: boolean;
    isModalOpened: boolean;
    activeModule?: Module;
    isLoading: boolean;
}
