export interface CriteriaResponse {
    children: Array<object>;
    context: object;
    display: string;
    id: string;
    leaf: boolean;
    selectable: boolean;
    termCodes: Array<object>;
}

export interface SubmittedItems {
    criteriaType: string;
    items: CriteriaResponse;
}

export interface SearchTreeOverlayContentData {
	activeTab: number | string;
	criteriaResponse: CriteriaResponse[] | null;
	selectedItems: CriteriaResponse[];
}
