import React, { Component } from 'react';
import moment from 'moment';

class Contribution extends Component {
    render() {
        const { url, title, state, updatedAt, projectName } = this.props;
        return (
            <article>
                <div className="title"><a href={`https://github.com/${projectName}`}><b>{projectName}</b></a> <a href={ url }>{ title }</a></div>
                <div className={`status ${state}`}>{ state.toUpperCase() }</div>
                <div className="date" title={updatedAt}>{ moment(updatedAt).fromNow() }</div>
            </article>
        );
    }
}


export default Contribution;
