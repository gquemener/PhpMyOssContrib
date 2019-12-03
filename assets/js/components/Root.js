import React from 'react';
import { Provider } from 'react-redux';
import { Route } from 'react-router';
import { BrowserRouter } from 'react-router-dom';
import AppContainer from './AppContainer';
import App from './App';

const Root = ({ store }) =>
    <Provider store={store}>
        <BrowserRouter>
            <AppContainer>
                <Route path='/' component={App} />
            </AppContainer>
        </BrowserRouter>
    </Provider>

export default Root;
