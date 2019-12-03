import React, { Component } from 'react';
import { connect } from 'react-redux';
import { withRouter } from 'react-router';
import queryString from 'query-string';
import Contribution from './Contribution';
import Pagination from './Pagination';
import * as actions from '../actions';
import * as store from '../configureStore';

class AppContainer extends Component {
    componentDidMount() {
        const dispatchPageAndFilter = (search) => {
            const { page, org } = queryString.parse(search);
            this.props.moveToPage(parseInt(page) || 1);
            this.props.filterByOrg(org || null);
        }

        dispatchPageAndFilter(this.props.location.search);

        this.unlisten = this.props.history.listen(
            (location, action) => dispatchPageAndFilter(location.search)
        );
    }

    componentWillUnmount() {
        this.unlisten();
    }

    render() {
        return (
            <div>
                { this.props.children }
            </div>
        );
    }
}

export default withRouter(connect(() => ({}), actions)(AppContainer));
