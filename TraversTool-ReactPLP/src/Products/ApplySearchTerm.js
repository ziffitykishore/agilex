import React, { useEffect, useState } from 'react';
import VirtualSearchBox from '../shared/DefaultRefinements/VirtualSearchBox';
import qs from 'qs';

const ApplySearchTerm = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const queryUrl = window.location.search.slice(1);
  const searchObject = qs.parse(queryUrl);
  const searchTermOnSearch = searchObject.q;
  const searchTermOnPLP = searchObject.search;
  const search = searchTermOnSearch || searchTermOnPLP;

  useEffect(() => {
    setSearchTerm(search)
  }, []);

  return <VirtualSearchBox defaultRefinement={searchTerm} />
}

export default ApplySearchTerm;
