/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

export interface FeasibilityQueryBuilderData {
    inclusionSearchInputText: string;
    exclusionSearchInputText: string;
    searchInputText: string;
    isFilterPanelOpen: boolean;
    isOntologyPanelOpen: boolean;
    criteriaOverlayType: string;
    requestWarning: string;
    isStateEditFilter: boolean;
    editFilterInformation?: {
        id: string;
        index: string | number;
        criteriaType: string;
    }
    isInputTextCleared: boolean;
    imgDelete: string;
}
