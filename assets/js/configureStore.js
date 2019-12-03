import { compose, createStore, combineReducers, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';

const filterByOrg = (contributions, org) => {
    if (org) {
        return contributions.filter(c => c.projectName.substr(0, c.projectName.indexOf('/')) === org);
    }

    return contributions;
}

export const getContributions = ({ _all, _org, _page }) => {
    return filterByOrg(_all, _org).slice((_page - 1) * 10, (_page - 1) * 10 + 10);
}

export const getPagesCount = ({ _all, _org }) => {
    return Math.ceil(filterByOrg(_all, _org).length / 10);
}

export const getOpenedCount = (contributions) => {
    return contributions.openedCount;
}

const configureStore = () => {
    const contributions = (state, action) => {
        const openedCounter = (acc, cur) => 'opened' === cur.state ? acc + 1 : acc;

        const groupByOrg = (acc, cur) => {
            const org = cur.projectName.substr(0, cur.projectName.indexOf('/'));
            if (!acc.hasOwnProperty(org)) {
                acc[org] = { name: org, contribs: 0, opened: 0 };
            }

            if ('opened' === cur.state) {
                acc[org].opened++;
            }
            if (org === 'prooph') {
                let a = 1;
            }

            acc[org].contribs++;

            return acc;
        };

        if (undefined === state) {
            return {
                openedCount: 0,
                orgs: [],
                _all: [],
                _org: null,
                _page: 1,
            };
        }

        switch (action.type) {
            case 'RECEIVE_CONTRIBUTIONS':
                return Object.assign({}, state, {
                    openedCount: action.contributions.reduce(openedCounter, 0),
                    orgs: Object.values(action.contributions.reduce(groupByOrg, {}))
                        .sort((a, b) => {
                            if (a.name < b.name) {
                                return -1;
                            }

                            if (a.name > b.name) {
                                return 1;
                            }

                            return 0;
                        })
                        .sort((a, b) => b.contribs - a.contribs)
                        .sort((a, b) => b.opened - a.opened)
                        .slice(0, 25),
                    _all: action.contributions,
                });

            case 'MOVE_TO_PAGE':
                return Object.assign({}, state, {
                    _page: action.page
                });

            case 'FILTER_BY_ORG':
                return Object.assign({}, state, {
                    _org: action.org
                });

            default:
                return state;
        }
    };

    const fetching = (state = false, action) => {
        switch (action.type) {
            case 'FETCH_CONTRIBUTIONS':
                return true;

            case 'RECEIVE_CONTRIBUTIONS':
                return false;

            default:
                return state;
        }
    };

    const composeEnhancers =
        typeof window === 'object' &&
        window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ ?
        window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__({}) :
        compose;

    return createStore(
        combineReducers({ contributions, fetching }),
        composeEnhancers(applyMiddleware(thunk))
    );
};

export default configureStore;
