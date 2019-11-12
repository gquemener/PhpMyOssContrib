import { compose, createStore, combineReducers, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';

const configureStore = () => {
    const contributions = (state = [], action) => {
        switch (action.type) {
            case 'RECEIVE_CONTRIBUTIONS':
                return action.contributions;

            default:
                return state;
        }
    };

    const pagesCount = (state = 0, action) => {
        switch (action.type) {
            case 'RECEIVE_CONTRIBUTIONS':
                return action.pagesCount;

            default:
                return state;
        }
    };

    const openedCount = (state = 0, action) => {
        switch (action.type) {
            case 'RECEIVE_CONTRIBUTIONS':
                return action.openedCount;

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

    const projects = (state = [], action) => {
        switch (action.type) {
            case 'RECEIVE_CONTRIBUTIONS':

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
        combineReducers({ contributions, pagesCount, openedCount, fetching, projects }),
        composeEnhancers(applyMiddleware(thunk))
    );
};

export default configureStore;
