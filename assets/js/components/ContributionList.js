import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter } from 'react-router';
import queryString from 'query-string';
import Contribution from './Contribution';
import Pagination from './Pagination';
import * as actions from '../actions';
import * as store from '../configureStore';

class ContributionList extends Component {
    componentDidMount() {
        const { page } = queryString.parse(this.props.location.search);

        this.props.moveToPage(parseInt(page) || 1);
        this.props.fetchContributions();
    }

    render() {
        const contributions = store.getContributions(this.props.contributions);

        return (
            <div className="contributions">
                {
                    contributions.map((value) => {
                        return <Contribution key={value.id} {...value} />;
                    })
                }
                <Pagination />
            </div>
        );
    }
}

const mapStateToProps = ({ contributions }) => ({ contributions });

export default withRouter(connect(mapStateToProps, actions)(ContributionList));
