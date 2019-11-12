import { compose, createStore, combineReducers, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';

export const getContributions = (contributions) => {
    return contributions._all[contributions.page - 1] || [];
}

export const getPagesCount = (contributions) => {
    return contributions._all.length;
}

export const getOpenedCount = (contributions) => {
    return contributions.openedCount;
}

const configureStore = () => {
    const contributions = (state, action) => {
        if (undefined === state) {
            return {
                page: 1,
                openedCount: 0,
                orgs: [],
                _all: [],
            };
        }

        const grouper = (acc, cur) => {
            if (0 === acc.length) {
                acc[0] = [];
            }

            if (acc[acc.length - 1].length < 10) {
                acc[acc.length - 1].push(cur);
            } else {
                acc[acc.length] = [cur];
            }

            return acc;
        };

        const openedCounter = (acc, cur) => 'opened' === cur.state ? acc + 1 : acc;

        const groupByOrg = (acc, cur) => {
            const org = cur.projectName.substr(0, cur.projectName.indexOf('/'));
            if (!acc.hasOwnProperty(org)) {
                acc[org] = { name: org, contribs: 0, opened: 0 };
            }

            if ('opened' === cur.state) {
                acc[org].opened++;
            }

            acc[org].contribs++;

            return acc;
        };

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
                    _all: action.contributions.reduce(grouper, []),
                });

            case 'MOVE_TO_PAGE':
                return Object.assign({}, state, {
                    page: action.page
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
