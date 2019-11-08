import React from 'react';
import { connect } from 'react-redux';
import PaginationLink from './PaginationLink';

class Pagination extends React.Component {
    render() {
        return (
            <div className="pagination">
                {
                    [...Array(this.props.pagesCount).keys()].map(index => <PaginationLink key={index} page={index + 1} />)
                }
            </div>
        )
    }
}

const mapStateToProps = ({ pagesCount }) => ({ pagesCount });

export default connect(mapStateToProps)(Pagination);
