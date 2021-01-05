import { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { connectHits } from 'react-instantsearch-dom';
import { fetchContent } from '../ApiHelpers';

function LoadSpotPricing({
  addPricing,
  apiUrls,
  categoryId,
  hits,
  isLoadingAttributes,
  pricing,
  shouldTriggerSpotPricing
}) {
  const [skus, setSkus] = useState();
  const spotPricingTrigger = categoryId ? isLoadingAttributes : shouldTriggerSpotPricing;

  useEffect(() => {
    setSkus(hits.map(hit => {
      if (Array.isArray(hit.sku)) {
        return hit.sku[0];
      }

      return hit.sku;
    }).filter(sku => {
      // Only SKUs for which we don't already have pricing
      return !pricing.find(product => product.sku === sku);
    }));
  }, [spotPricingTrigger]);

  useEffect(() => {
    if (skus) {
      // fetchContent(apiUrls.pricing.url, skus.toString()).then(newPricing => {
      //   addPricing(newPricing);
      // });
    }
  }, [skus]);

  return null;
}

LoadSpotPricing.propTypes = {
  addPricing: PropTypes.func.isRequired,
  apiUrls: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  hits: PropTypes.array.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  pricing: PropTypes.array.isRequired,
  shouldTriggerSpotPricing: PropTypes.bool,
};

export default connectHits(LoadSpotPricing);
