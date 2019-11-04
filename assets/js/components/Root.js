import React from 'react';
import Menu from './Menu';
import ContributionList from './ContributionList';

const Root = () =>
    <div className="root">
        <h1 className="title">OSS Contributions</h1>
        <div className="wrapper">
            <Menu />
            <ContributionList />
        </div>
    </div>

export default Root;
