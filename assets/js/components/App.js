import React from 'react';
import { Link } from 'react-router-dom';
import Menu from './Menu';
import ContributionList from './ContributionList';

const App = ({ moveToPage }) =>
    <div className="container">
        <h1 className="title">
            <Link to="/">OSS Contributions</Link>
        </h1>
        <div className="wrapper">
            <Menu />
            <ContributionList />
        </div>
    </div>

export default App;
