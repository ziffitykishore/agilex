import React from 'react';
import { connectCurrentRefinements } from 'react-instantsearch-dom';
import PropTypes from 'prop-types';

const CurrentRefinements = ({ items, refine, createURL, filterAttributesInfo }) => {
  const metaRobots = document.querySelector('meta[name="robots"]');
  if (metaRobots) {
    metaRobots.setAttribute('content', items.length > 1 ? 'NOINDEX,NOFOLLOW' : 'INDEX,FOLLOW');
  }

  return (
    <ul className="current-refinements">
      {items.map(item => (
        <li key={item.label}>
          {item.items && (
            <React.Fragment>
              <ul>
                {item.items.map(nested => (
                  <li key={nested.label}>
                    <button
                      type="button"
                      className="clear-refinement-x"
                      onClick={event => {
                        event.preventDefault();
                        refine(nested.value);
                      }}
                    >×</button>
                      {filterAttributesInfo.map(attr => attr.id == item.id && `${attr.label}: `)}
                    <a
                      href={createURL(nested.value)}
                      rel="nofollow"
                      onClick={event => {
                        event.preventDefault();
                        refine(nested.value);
                      }}
                    >
                      {nested.label}
                    </a>
                  </li>
                ))}
              </ul>
            </React.Fragment>
          )}
        </li>
      ))}
    </ul>
  )
};

CurrentRefinements.propTypes = {
  createURL: PropTypes.func.isRequired,
  items: PropTypes.array.isRequired,
  refine: PropTypes.func.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
}

export default connectCurrentRefinements(CurrentRefinements);
