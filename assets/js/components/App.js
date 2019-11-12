import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import Menu from './Menu';
import ContributionList from './ContributionList';
import * as actions from '../actions';

const App = ({ moveToPage }) =>
    <div className="container">
        <h1 className="title">
            <Link to="/" onClick={moveToPage.bind(this, 1)}>OSS Contributions</Link>
        </h1>
        <div className="wrapper">
            <Menu />
            <ContributionList />
        </div>
    </div>

export default connect(() => ({}), actions)(App);
