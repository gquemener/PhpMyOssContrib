import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter } from 'react-router';
import queryString from 'query-string';
import Contribution from './Contribution';
import Pagination from './Pagination';
import * as api from '../api';

class ContributionList extends Component {
    componentDidMount() {
        const { page } = queryString.parse(this.props.location.search)

        api.fetchContributions(page)
            .then(this.props.onContributionsReceived);
    }

    render() {
        return (
            <div className="contributions">
                {
                    this.props.contributions.map((value) => {
                        return <Contribution key={value.id} {...value} />;
                    })
                }
                <Pagination />
            </div>
        );
    }
}

const mapStateToProps = (state) => ({
    contributions: state
});

const mapDispatchToProps = (dispatch) => ({
    onContributionsReceived: (contributions) => dispatch({ type: 'CONTRIBUTIONS_RECEIVED', contributions })
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(ContributionList));
