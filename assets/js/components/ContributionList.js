import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter } from 'react-router';
import queryString from 'query-string';
import Contribution from './Contribution';
import Pagination from './Pagination';
import * as actions from '../actions';

class ContributionList extends Component {
    componentDidMount() {
        const { page } = queryString.parse(this.props.location.search);

        this.props.fetchContributions(page);
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

const mapStateToProps = ({ contributions }) => ({ contributions });

export default withRouter(connect(mapStateToProps, actions)(ContributionList));
