import React from 'react';
import VirtualMenu from './VirtualMenu';
import PropTypes from 'prop-types';

// This essentially functions as a refinement list that is hidden so the user can't modify it.
// We use this to only render the relevant products to show on a PLP

export default function DefaultRefinements(props) {
  return (
    <VirtualMenu attribute="categoryIds" defaultRefinement={props.categoryId && props.categoryId.toString()} />
  );
};

DefaultRefinements.propTypes = {
  categoryId: PropTypes.number,
}
