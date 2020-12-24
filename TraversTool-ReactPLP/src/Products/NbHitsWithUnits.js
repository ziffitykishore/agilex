import React from 'react';
import { connectStateResults } from 'react-instantsearch-dom';
import PropTypes from 'prop-types';

// Returns a string with the hit count and pluralizes the unit.

const NbHitsWithUnits = ({ searchResults, unit }) => {
  if (!searchResults) {
    return null;
  }

  const { nbHits } = searchResults;

  return (
    <React.Fragment>
      {nbHits}
      {' '}
      {nbHits === 1 ? (
        <React.Fragment>{unit ? `${unit}` : 'item'}</React.Fragment>
      ) : (
        <React.Fragment>{unit ? `${unit}s` : 'items'}</React.Fragment>
      )}
    </React.Fragment>
  );
};

NbHitsWithUnits.propTypes = {
  searchResults: PropTypes.object,
  unit: PropTypes.string,
}

export default connectStateResults(NbHitsWithUnits);
