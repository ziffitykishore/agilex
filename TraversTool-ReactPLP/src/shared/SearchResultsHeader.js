import React from 'react';
import qs from 'qs';

const SearchResultsHeader = () => {
  const searchObject = qs.parse(window.location.search.slice(1));
  const searchTerm = searchObject.q;

  if (!searchTerm || searchTerm && searchTerm.length < 1) {
    return null;
  }

  return (
    <div>
      <h1 className="search-results-header">Search results for: {searchTerm}</h1>
    </div>
  )
}

export default SearchResultsHeader;
