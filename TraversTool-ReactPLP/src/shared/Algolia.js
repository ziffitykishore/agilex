import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import SearchClient from './SearchClient';
import { InstantSearch } from 'react-instantsearch-dom';

// This allows us to easily choose which index we're using (Search, Catland, PLP)
// https://www.algolia.com/doc/api-reference/widgets/instantsearch/react/#widget-param-searchclient

class Algolia extends PureComponent {
  render() {
    return (
      <SearchClient.Consumer>
        {searchClient => (
          <InstantSearch
            searchClient={searchClient}
            {...this.props}
          />
        )}
      </SearchClient.Consumer>
    )
  }
}

Algolia.propTypes = {
  indexName: PropTypes.string.isRequired,
};

export default Algolia;
