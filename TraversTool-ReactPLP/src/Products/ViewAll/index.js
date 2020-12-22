import React from 'react';
import ViewAllPropProvider from './ViewAllPropProvider';
import { connectStateResults } from 'react-instantsearch-dom';
import PropTypes from 'prop-types';

function ViewAll({ searchResults, props }) {
  if (!searchResults) {
    return null;
  }

  return (
    <React.Fragment>
      <ViewAllPropProvider
        defaultRefinement={props.min}
        items={[]}
        max={searchResults.nbHits}
        min={props.min}
        setTableExpanded={props.setTableExpanded}
        tableExpanded={props.tableExpanded}
        productsShowingCount={props.productsShowingCount}
      />
    </React.Fragment>
  );
};

ViewAll.propTypes = {
  defaultRefinement: PropTypes.number,
  min: PropTypes.number,
  productsShowingCount: PropTypes.number,
  props: PropTypes.object.isRequired,
  searchResults: PropTypes.object,
  setTableExpanded: PropTypes.func,
  tableExpanded: PropTypes.bool,
};

export default connectStateResults(ViewAll);
