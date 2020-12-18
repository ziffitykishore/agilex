import Algolia from '../../../shared/Algolia';
import List from '../List';
import PropTypes from 'prop-types';
import React, { PureComponent } from 'react';
import { Configure } from 'react-instantsearch-dom';
import SetHitCount from '../../SetHitCount';
import VirtualSearchBox from '../../../shared/DefaultRefinements/VirtualSearchBox';

class GroupedList extends PureComponent {
  setNbHits = (nbHits) => {
    this.props.setNbHits({
      [this.props.valueKey]: nbHits
    });
  }

  render() {
    return (
      <React.Fragment>
        <Algolia
          indexName={this.props.indexName}
        >
          <Configure facetFilters={this.props.facetFilters} />
          <SetHitCount setNbHits={this.setNbHits} view={this.props.view} />
          <VirtualSearchBox defaultRefinement={this.props.searchTerm} />
          <List
            apiUrls={this.props.apiUrls}
            categoryId={this.props.categoryId}
            currency={this.props.currency}
            customerGroupId={this.props.customerGroupId}
            deduplicate={this.props.deduplicate}
            filterAttributesInfo={this.props.filterAttributesInfo}
            groupName={this.props.groupName}
            groups={this.props.groups}
            isLoadingAttributes={this.props.isLoadingAttributes}
            listAttributes={this.props.listAttributes}
            pricing={this.props.pricing}
            quantity={this.props.quantity}
            setGroupImage={this.props.setGroupImage}
            setQuantity={this.props.setQuantity}
            urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
            viewMoreMinimum={this.props.viewMoreMinimum}
          />
        </Algolia>
      </React.Fragment>
    );
  };
};

GroupedList.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  currency: PropTypes.string.isRequired,
  categoryId: PropTypes.number,
  customerGroupId: PropTypes.number.isRequired,
  deduplicate: PropTypes.func.isRequired,
  facetFilters: PropTypes.array,
  filterAttributesInfo: PropTypes.array.isRequired,
  groupDescriptions: PropTypes.object.isRequired,
  groupName: PropTypes.string.isRequired,
  groups: PropTypes.array.isRequired,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  listAttributes: PropTypes.array.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  searchResults: PropTypes.object,
  searchState: PropTypes.object,
  searchTerm: PropTypes.string,
  setDefaultGroupQuantity: PropTypes.func,
  setGroupImage: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired,
  setNbHits: PropTypes.func,
  nbHits: PropTypes.object,
  urlToGroupingImagesCatalog: PropTypes.string,
  valueKey: PropTypes.string,
  view: PropTypes.string.isRequired,
  viewMoreMinimum: PropTypes.number
};

export default GroupedList;
