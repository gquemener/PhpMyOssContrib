import React from 'react';
import { Link } from 'react-router-dom';
import { withRouter } from 'react-router';

const PaginationLink = ({ page, location, active }) => {
    const query = new URLSearchParams(location.search);
    query.set('page', page);

    let className = '';
    if (active) {
        className = 'active';
    }

    return <Link className={className} to={{ search: query.toString() }}>{page}</Link>
};

export default withRouter(PaginationLink);
