import React from 'react';
import { Link } from 'react-router-dom';
import { withRouter } from 'react-router';

const PaginationLink = ({ page, location }) => {
    const query = new URLSearchParams(location.search);
    query.set('page', page);

    return <Link to={{ search: query.toString() }}>{page}</Link>
};

export default withRouter(PaginationLink);
