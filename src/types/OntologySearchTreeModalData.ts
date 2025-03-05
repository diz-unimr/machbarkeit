/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import type { ConceptType } from './ConceptOptionsData.ts'
import type { QuantityType } from '../types/QuantityOptionsData.ts'
import type { TimeRangeType } from '../types/TimeRangeOptionsData.ts'

export interface Criterion {
    children?: Array<Criterion> | undefined;
    id: number;
    moduleId: number;
    parentId: number | null;
    display: string;
    termCodes: {
        code: string;
        system: string;
        display: string;
        version: string;
    }[],
    context: {
        code: string;
        system: string;
        version: string;
        display: string;
    };
    code: string;
    codeSystem: string;
    swlCode: string | undefined;
    selectable: boolean;
    leaf: boolean;
    timeRestrictionAllowed: boolean | undefined;
    filterName: string;
    filterType: string;
    filterOptions: {
        code: string;
        display: string;
        system: string | undefined;
        version: string | undefined;
    }[];
    selectedFilter?: ConceptType | QuantityType | TimeRangeType;
}

export interface Modules {
    id: number;
    moduleName: string;
    version: string;

    /*
    id: string;
    kds_module_name: string;
    fdpg_kds_code: string;
    fdpg_kds_system: string;
    fdpg_kds_version: string:
    version: string;
    */
}

export interface OntologyTree {
    display: string;
    id: number;
    leaf: boolean;
    moduleId: number;
    parentId: number | null;
    selectable: boolean;
}

export interface OntologySearchTreeModalData {
	activeTab: number;
    wasTabClicked: Array<boolean>;
	ontologyResponse: Criterion[] | null;
	selectedItems: Criterion[];
    isSearchResultNoData: Array<boolean>;
    modules: Modules[] | null;
    ontologyTree: Criterion[] | null;
    ontologyTreeSearch: Array<Criterion | null>;
    context: {
        code: string;
        system: string;
        version: string;
        display: string;
    } | null;
}
