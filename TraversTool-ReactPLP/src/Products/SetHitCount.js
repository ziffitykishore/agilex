import { connectStateResults } from 'react-instantsearch-dom';
import { useEffect } from 'react';
import PropTypes from 'prop-types';

const SetHitCount = connectStateResults((props) => {
  if (!props.searchResults) {
    return null;
  }

  useEffect(() => {
    props.setNbHits(props.searchResults.nbHits);
  }, [props.searchResults.nbHits, props.view])

  return null;
});

export default SetHitCount;

SetHitCount.propTypes = {
  setNbHits: PropTypes.func.isRequired,
  view: PropTypes.string.isRequired
}
