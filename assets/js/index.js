require('../css/index.css');

import React from 'react';
import ReactDOM from 'react-dom';
import Root from './components/Root'
import { createStore } from 'redux';

const reducer = (state = [], action) => {
    switch (action.type) {
        case 'CONTRIBUTIONS_RECEIVED':
            return action.contributions;

        default:
            return state;
    }
};

const store = createStore(reducer);

ReactDOM.render(
    <Root store={store} />,
    document.getElementById('app')
);
