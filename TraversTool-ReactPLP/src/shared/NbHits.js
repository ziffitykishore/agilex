import React from 'react';
import { connectStateResults } from 'react-instantsearch-dom';
import PropTypes from 'prop-types';

// Returns the number of products

const NbHits = ({ searchResults }) => {
  if (!searchResults) {
    return null;
  }

  const { nbHits } = searchResults;

  return (
    <React.Fragment>
      {nbHits}
    </React.Fragment>
  );
};

NbHitsWithUnits.propTypes = {
  searchResults: PropTypes.object,
}

export default connectStateResults(NbHits);
