import Algolia from '../Algolia';
import PropTypes from 'prop-types';
import React from 'react';
import Hits from './Hits';
import VirtualRefinementList from '../VirtualRefinementList';
import classNames from 'classnames';

const Breadcrumbs = ({
  categoriesIndexName,
  category,
  setCategoryId,
  showProductsSidebar
}) => {
  const parentCategoryIds = category.parent_category_ids || [];
  if (!category || parentCategoryIds.length <= 0) {
    return null;
  }

  const breadcrumbClasses = classNames({
    'plp-breadcrumbs': true,
    'sidebar__closed': !showProductsSidebar,
  });

  return (
    <Algolia indexName={categoriesIndexName}>
      <VirtualRefinementList
        attribute="category_id"
        defaultRefinement={parentCategoryIds}
        operator="or"
      />
      <div className={breadcrumbClasses}>
      <div className="breadcrumbs-content">
        <a href="/">Home</a>
      </div>
        <Hits setCategoryId={setCategoryId} />
        <span className="breadcrumbs__current">{category.name}</span>
      </div>
    </Algolia>
  );
};

Breadcrumbs.propTypes = {
  categoriesIndexName: PropTypes.string.isRequired,
  category: PropTypes.object.isRequired,
  setCategoryId: PropTypes.func.isRequired,
  showProductsSidebar: PropTypes.bool
}

export default Breadcrumbs;
