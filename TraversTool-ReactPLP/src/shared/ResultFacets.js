import PropTypes from 'prop-types';
import { connectStateResults } from 'react-instantsearch-dom';

const ResultFacets = ({
  searchResults,
  onFacets,
}) => {
  if (!searchResults) {
    return null;
  }

  onFacets(searchResults.disjunctiveFacets);
  return null;
};

ResultFacets.propTypes = {
  onFacets: PropTypes.func.isRequired,
};

export default connectStateResults(ResultFacets);
