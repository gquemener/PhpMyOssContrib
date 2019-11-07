import React from 'react';
import Menu from './Menu';
import ContributionList from './ContributionList';

const App = () =>
    <div className="container">
        <h1 className="title">OSS Contributions</h1>
        <div className="wrapper">
            <Menu />
            <ContributionList />
        </div>
    </div>

export default App;
