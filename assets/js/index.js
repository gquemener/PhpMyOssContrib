require('../css/index.css');

import React from 'react';
import ReactDOM from 'react-dom';
import Root from './components/Root'
import { compose, createStore, combineReducers, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';

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

const composeEnhancers =
    typeof window === 'object' &&
    window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ ?
    window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__({}) :
    compose;

const store = createStore(combineReducers({ contributions, pagesCount }), composeEnhancers(applyMiddleware(thunk)));

ReactDOM.render(
    <Root store={store} />,
    document.getElementById('app')
);
