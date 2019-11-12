require('../css/index.css');

import React from 'react';
import ReactDOM from 'react-dom';
import Root from './components/Root'
import configureStore from './configureStore';

ReactDOM.render(
    <Root store={configureStore()} />,
    document.getElementById('app')
);
