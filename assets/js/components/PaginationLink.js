import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import * as actions from '../actions';

const PaginationLink = ({ page, moveToPage }) => (
    <Link to={{ search: `page=${page}` }} onClick={moveToPage.bind(this, page)}>{page}</Link>
);

export default connect(() => ({}), actions)(PaginationLink);
