import * as api from './api';

export const fetchContributions = (page = 1) => (dispatch) => {
    dispatch({ type: 'FETCH_CONTRIBUTIONS', page });
    api.fetchContributions(page)
        .then(response => dispatch({ type: 'RECEIVE_CONTRIBUTIONS', ...response }));
};
