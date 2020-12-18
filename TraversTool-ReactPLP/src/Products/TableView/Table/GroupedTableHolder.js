import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import GroupedTable from './GroupedTable';
import equals from 'is-equal-shallow';
import { facetFilters } from '../../helpers';

const simpleEquals = (a, b) => {
  if (a === b) {
    return true;
  }
  if (!a || !b) {
    return false;
  }
  return Object.keys(a).length === 0 && Object.keys(b).length === 0;
};

// This component translates the categoryId, grouping refinements, and user selected refinements
// into actual filters.  This prevents unnecessary re-rendering of the GroupedTable.
class GroupedTableHolder extends PureComponent {
  state = {
    facetFilters: null,
    lastGroupRefinements: null,
    lastRefinementList: null,
    lastCategoryId: null,
  }

  static getDerivedStateFromProps(props, state) {
    // The facetFilters value is composed of the below values.  We rebuild it if any of them change.
    const catChange = props.categoryId !== state.lastCategoryId;
    const refinementChange = !simpleEquals(props.searchState.refinementList, state.lastRefinementList);
    const groupedRefinementsChange = !equals(props.groupRefinements, state.lastGroupRefinements);

    if (catChange || refinementChange || groupedRefinementsChange) {
      // Compose together the refinement state.
      const searchState = {
        ...props.groupRefinements,
        ...props.searchState.refinementList,
        categoryIds: props.categoryId
      };

      // Modify the facetFilters state, and track old values to know when to update it.
      return {
        facetFilters: facetFilters(searchState),
        lastGroupRefinements: props.groupRefinements,
        lastRefinementList: props.searchState.refinementList,
        lastCategoryId: props.categoryId,
      };
    }

    // The props that compose facetFilters didn't change, so modify zero state values.
    return null;
  }

  render() {
    // This just uses the cached facetFilters derived from props.
    return (
      <GroupedTable
        currency={this.props.currency}
        customerGroupId={this.props.customerGroupId}
        facetFilters={this.state.facetFilters}
        filterAttributesInfo={this.props.filterAttributesInfo}
        groupName={this.props.groupName}
        indexName={this.props.indexName}
        isLoadingAttributes={this.props.isLoadingAttributes}
        pricing={this.props.pricing}
        searchTerm={this.props.searchTerm}
        selectProduct={this.props.selectProduct}
        selectedProduct={this.props.selectedProduct}
        setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
        setGroupImage={this.props.setGroupImage}
        setNbHits={this.props.setNbHits}
        tableAttributes={this.props.tableAttributes}
        urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
        valueKey={this.props.valueKey}
        view={this.props.view}
        viewMoreMinimum={15}
      />
    );
  }
}

GroupedTableHolder.propTypes = {
  categoryId: PropTypes.number.isRequired,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  groupName: PropTypes.string,
  groupRefinements: PropTypes.object.isRequired,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  pricing: PropTypes.array.isRequired,
  searchState: PropTypes.object.isRequired,
  searchTerm: PropTypes.string,
  selectProduct: PropTypes.func.isRequired,
  selectedProduct: PropTypes.object.isRequired,
  setDefaultGroupQuantity: PropTypes.func,
  setGroupImage: PropTypes.func.isRequired,
  setNbHits: PropTypes.func.isRequired,
  tableAttributes: PropTypes.array.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
  valueKey: PropTypes.string.isRequired,
  view: PropTypes.string.isRequired,
  viewMoreMinimum: PropTypes.number,
};

export default GroupedTableHolder;
