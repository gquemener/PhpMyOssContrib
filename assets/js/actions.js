import * as api from './api';

export const fetchContributions = (page = 1) => (dispatch) => {
    dispatch({ type: 'FETCH_CONTRIBUTIONS' });
    api.fetchContributions()
        .then(response => dispatch({ type: 'RECEIVE_CONTRIBUTIONS', ...response }));
};

export const moveToPage = (page) => (dispatch) => {
    dispatch({ type: 'MOVE_TO_PAGE', page });
};

export const filterByOrg = (org) => (dispatch) => {
    dispatch({ type: 'FILTER_BY_ORG', org });
};
