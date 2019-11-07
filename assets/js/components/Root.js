import React from 'react';
import { Provider } from 'react-redux';
import { Route } from 'react-router';
import { BrowserRouter } from 'react-router-dom';
import App from './App';

const Root = ({ store }) =>
    <Provider store={store}>
        <BrowserRouter>
            <div>
                <Route path='/' component={App} />
            </div>
        </BrowserRouter>
    </Provider>

export default Root;
