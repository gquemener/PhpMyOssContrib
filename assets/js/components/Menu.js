import React from 'react';
import { connect } from 'react-redux';
import Loading from './Loading';

const Menu = (props) =>
    <div className="menu">
        <Loading active={props.fetching} />
        <div className="gauge">
            <div className="figure">{props.openedCount}</div>
            <div className="label">opened PRs</div>
        </div>
        <ul>
            {
                props.projects.map(p => 
                    <li><a href="#">{p}</a> <span className="pulls-count">0</span></li>
                )
            }
        </ul>
    </div>

const mapStateToProps = ({ openedCount, fetching, projects }) => ({ openedCount, fetching, projects });

export default connect(mapStateToProps)(Menu);
