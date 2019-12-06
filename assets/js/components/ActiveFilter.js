import React from 'react';
import { Link } from 'react-router-dom';

const ActiveFilter = ({ name }) =>
    <div className="activeFilter" style={{ opacity: name ? 1 : 0 }}>
        <span className="name">{name}</span>
        <Link to="/" className="remove">x</Link>
    </div>

export default ActiveFilter;
