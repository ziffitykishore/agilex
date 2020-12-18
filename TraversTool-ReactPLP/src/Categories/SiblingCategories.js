import Algolia from '../shared/Algolia';
import { Hits } from 'react-instantsearch-dom';
import React from 'react';
import PropTypes from 'prop-types';
import VirtualMenu from '../shared/DefaultRefinements/VirtualMenu';

// The categories that show in the catland sidebar are the categories on the previous level
function SiblingCategories({ parentCategoryId = -1, categoryId, collapseDropdown, indexName, setCategoryId }) {
  return (
    <div className="sibling-buttons">
      <Algolia indexName={indexName}>
        <VirtualMenu attribute="parent_category_id" defaultRefinement={parentCategoryId && parentCategoryId.toString()} />
        <Hits hitComponent={({ hit }) => (
          hit.product_count > 0 && (
            <a
              href={hit.url}
              onClick={(e) => {
                e.preventDefault();
                collapseDropdown();
                setCategoryId({ hit });
              }}
              className={categoryId && categoryId.toString() === hit.category_id.toString() ? "active" : "inactive"}
            >
              {hit.name}
            </a>
          )
        )} />
      </Algolia>
    </div>
  );
}

SiblingCategories.propTypes = {
  categoryId: PropTypes.number.isRequired,
  collapseDropdown: PropTypes.func.isRequired,
  indexName: PropTypes.string.isRequired,
  parentCategoryId: PropTypes.number.isRequired,
  setCategoryId: PropTypes.func.isRequired,
};

export default SiblingCategories;
