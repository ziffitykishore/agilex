import React from 'react';
import { connectHits } from 'react-instantsearch-dom';
import orderBy from 'lodash.orderby';

const Hits = connectHits(({ setCategoryId, hits }) => {
  const sortedHits = orderBy(hits, 'level');

  return (
    <div className="breadcrumbs-content">
      {sortedHits.map(hit => (
        <a
          key={hit.name}
          href={hit.url}
          onClick={(e) => {
            e.preventDefault();
            setCategoryId({ hit });
          }}
        >
          {hit.name}
        </a>
      ))}
    </div>
  )
});

export default Hits;