import React from 'react';
import { connect } from 'react-redux';
import PaginationLink from './PaginationLink';
import * as store from '../configureStore';

class Pagination extends React.Component {
    render() {
        return (
            <div className="pagination">
                {
                    [...Array(store.getPagesCount(this.props.contributions)).keys()].map(index => <PaginationLink key={index} page={index + 1} active={store.isCurrentPage(this.props.contributions, index + 1)}/>)
                }
            </div>
        )
    }
}

const mapStateToProps = ({ contributions }) => ({ contributions });

export default connect(mapStateToProps)(Pagination);
