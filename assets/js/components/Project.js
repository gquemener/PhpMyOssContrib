import React from 'react';
import { Link } from 'react-router-dom';

const Project = ({ name, contribs, opened }) =>
    <>
        <Link to={{ search: `org=${name}` }}>{name}</Link>
        <span className="contribs-count">{contribs}</span>
        { 0 < opened ? <span className="opened-count">{opened}</span> : '' }
    </>

export default Project;
