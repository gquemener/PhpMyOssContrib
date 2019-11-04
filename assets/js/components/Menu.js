import React from 'react';

const Menu = () =>
    <div className="menu">
        <div className="gauge">
            <div className="figure">25</div>
            <div className="label">opened PRs</div>
        </div>
        <ul>
            <li><a href="#">phpspec/phpspec</a> <span className="pulls-count">8</span></li>
            <li><a href="#">prooph/event-store</a> <span className="pulls-count">3</span></li>
        </ul>
    </div>

export default Menu;
