import React from 'react';
import { connect } from 'react-redux';
import Loading from './Loading';
import * as store from '../configureStore';

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
                        <a href="#">{props.contributions.orgs[key].name}</a>
                        <span className="contribs-count">{props.contributions.orgs[key].contribs}</span>
                        { 0 < props.contributions.orgs[key].opened ? <span className="opened-count">{props.contributions.orgs[key].opened}</span> : '' }
                    </li>
                )
            }
        </ul>
    </div>

const mapStateToProps = ({ contributions, fetching }) => ({ contributions, fetching });

export default connect(mapStateToProps)(Menu);
