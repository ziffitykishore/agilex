import { useEffect } from 'react';
import PropTypes from 'prop-types';
import { connectHits } from 'react-instantsearch-dom';
import { renderHitProperty } from './helpers';

/*
  Sets the default quantity for items in non-grouped PLPs. We set it to the minimum
  sale quantity if there is one, the minimum quantity increment if there is one, or
  we default to 1 for all other products
*/

function DefaultQuantity({
  quantity,
  setDefaultQuantities,
  hits,
}) {
  useEffect(() => {
    const newSkus = hits.map(hit => {
      return {
        ...hit,
        sku: renderHitProperty(hit.sku),
      };
    }).filter(hit => {
      // Only SKUs for which we don't already have quantity
      return !(hit.sku in quantity);
    });

    const quantities = newSkus.reduce((quantities, hit) => {
      quantities[hit.sku] = Math.max(hit.min_sale_qty, hit.qty_increment) || 1;
      return quantities;
    }, {});

    setDefaultQuantities(quantities);
  }, [hits]);

  return null;
}

DefaultQuantity.propTypes = {
  hits: PropTypes.array.isRequired,
  setDefaultQuantities: PropTypes.func.isRequired,
  quantity: PropTypes.object.isRequired,
};

export default connectHits(DefaultQuantity);