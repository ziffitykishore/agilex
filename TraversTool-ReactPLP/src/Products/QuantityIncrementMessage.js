import React from 'react';
import PropTypes from 'prop-types';

const QuantityIncrementMessage = (props) => {
  const quantityIncrement = props.hit.qty_increment ? props.hit.qty_increment : props.hit.min_sale_qty;

  if (quantityIncrement && quantityIncrement > 1) {
    return (
      <strong className="increment-message">Sold in increments of {quantityIncrement}</strong>
    );
  } else {
    return null;
  }
}

QuantityIncrementMessage.propTypes = {
  qty_increment: PropTypes.number,
}

export default QuantityIncrementMessage;
