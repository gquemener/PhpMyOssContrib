import React from 'react';
import { connect } from 'react-redux';
import Loading from './Loading';
import * as store from '../configureStore';
import Project from './Project';

const Menu = (props) =>
    <div className="menu">
        <div className="gauge">
            <div className="figure">{store.getOpenedCount(props.contributions)}</div>
            <div className="label">opened PRs</div>
        </div>
        <Loading active={props.fetching} />
        <ul>
            {
                Object.keys(props.contributions.orgs).map(key =>
                    <li key={key}>
                        <Project {...props.contributions.orgs[key]} />
                    </li>
                )
            }
        </ul>
    </div>

const mapStateToProps = ({ contributions, fetching }) => ({ contributions, fetching });

export default connect(mapStateToProps)(Menu);
