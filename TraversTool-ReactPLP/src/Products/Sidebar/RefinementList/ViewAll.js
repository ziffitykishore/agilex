import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const ViewAll = props => {
  const viewAllClasses = classNames({
    'view-all': true,
    'view-all--disabled': props.disabled,
  });

  return (
    <button
      className={viewAllClasses}
      onClick={props.onShowMoreClick}
    >
      {props.extended ? 'View Less' : 'View More'}
    </button>
  );
};

ViewAll.propTypes = {
  disabled: PropTypes.bool.isRequired,
  extended: PropTypes.bool.isRequired,
  onShowMoreClick: PropTypes.func.isRequired,
  showMore: PropTypes.bool,
};

export default ViewAll;
