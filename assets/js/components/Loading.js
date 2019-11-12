import React from 'react';

require('../../css/loading.css');

const Loading = ({ active }) => {
    return <div className="loading-container">
        <div className="loading" style={{ opacity: active ? 1 : 0 }}>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
};

export default Loading;
