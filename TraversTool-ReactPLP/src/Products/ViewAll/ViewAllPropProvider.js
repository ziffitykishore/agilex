import React, { useEffect, useState } from 'react';
import { connectHitsPerPage } from 'react-instantsearch-dom';
import NbHitsWithUnits from '../NbHitsWithUnits';
import PropTypes from 'prop-types';

const ViewAllPropProvider = ({
  currentRefinement,
  max,
  min,
  productsShowingCount,
  refine,
  setTableExpanded,
  tableExpanded,
}) => {
  if (max < min || max === min) {
    return null;
  }

  const [scrollPosition, setScrollPosition] = useState(document.documentElement.scrollTop);

  useEffect(()=> {
    if (tableExpanded && currentRefinement !== max) {
      refine(max);
    }
  }, [tableExpanded]);

  useEffect(() => {
    if (productsShowingCount) {
      window.scrollTo(0, scrollPosition);
    }
  }, [productsShowingCount])

  return (
    <div>
      {currentRefinement === max ? (
        <button
          type="button"
          onClick={() => {
            refine(min);
            setTableExpanded && setTableExpanded(false);
          }}
        >
          View {min} SKUs
        </button>
      ) : (
        <button
          type="button"
          onClick={() => {
            setScrollPosition(document.documentElement.scrollTop);
            refine(max);
            setTableExpanded && setTableExpanded(true);
          }}
        >
          View all {`${max} SKUs` || <NbHitsWithUnits unit="SKU" />}
        </button>
      )}
    </div>
  )
};

ViewAllPropProvider.propTypes = {
  currentRefinement: PropTypes.number,
  max: PropTypes.number,
  min: PropTypes.number,
  productsShowingCount: PropTypes.number,
  refine: PropTypes.func.isRequired,
  setTableExpanded: PropTypes.func,
  tableExpanded: PropTypes.bool,
};

export default connectHitsPerPage(ViewAllPropProvider);
