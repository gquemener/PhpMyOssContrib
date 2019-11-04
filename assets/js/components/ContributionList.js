import React, { Component } from 'react';

import Contribution from './Contribution';

class ContributionList extends Component {
    constructor(props) {
        super(props);
        this.state = {
            contributions: [],
        };
    }

    componentDidMount() {
        fetch('/api/contributions')
            .then(response => response.json())
            .then(contributions => { 
                this.setState({ contributions });
            });
    }

    render() {
        return (
            <div className="contributions">
                {
                    this.state.contributions.map((value) => {
                        return <Contribution key={value.id} {...value} />;
                    })
                }
            </div>
        );
    }
}


export default ContributionList;
