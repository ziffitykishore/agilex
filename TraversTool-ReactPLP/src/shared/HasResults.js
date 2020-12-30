import PropTypes from 'prop-types';
import { connectStateResults } from 'react-instantsearch-dom';

const HasResults = ({
  loading = null,
  noResults = null,
  results,
  searchResults,
  searchState,
}) => {
  if (!searchResults) {
    return loading;
  }

  if (searchResults.nbHits !== 0) {
    return results;
  }

  return noResults;
};

HasResults.propTypes = {
  loading: PropTypes.element,
  noResults: PropTypes.element,
  results: PropTypes.element.isRequired,
};

export default connectStateResults(HasResults);
