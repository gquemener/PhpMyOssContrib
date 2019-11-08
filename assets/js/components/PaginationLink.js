import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import * as actions from '../actions';

const PaginationLink = ({ page, fetchContributions }) => (
    <Link to={{ search: `page=${page}` }} onClick={fetchContributions.bind(this, page)}>{page}</Link>
);

export default connect(() => ({}), actions)(PaginationLink);
