import Algolia from '../../shared/Algolia';
import Group from '../Group/index';
import List from './List';
import PropTypes from 'prop-types';
import React, { PureComponent } from 'react';
import GroupedList from './List/GroupedList';
import { facetFilters } from '../helpers';
import ProductsContextProvider from '../ProductsContext';

class ListView extends PureComponent {
  render() {
    return (
      <ProductsContextProvider>
      <React.Fragment>
        <div className="list-and-flyout">
          {this.props.groups.length ? (
            <Group
              descriptions={this.props.groupDescriptions}
              facetCounts={this.props.facetCounts}
              groupImages={this.props.groupImages}
              groups={this.props.groups}
              nbHits={this.props.nbHits}
              setNbHits={this.props.setNbHits}
            >
              {({
                // This combines the grouping refinements set in the admin with
                // the refinements the user selects from the refinementList
                groupName, refinements: groupRefinements, setNbHits, valueKey }) => {
                const searchState = {
                  ...groupRefinements,
                  ...this.props.searchState.refinementList,
                  categoryIds: this.props.categoryId
                };

                return (
                  <GroupedList
                    apiUrls={this.props.apiUrls}
                    categoryId={this.props.categoryId}
                    currency={this.props.currency}
                    customerGroupId={this.props.customerGroupId}
                    deduplicate={this.props.deduplicate}
                    facetCounts={this.props.facetCounts}
                    facetFilters={facetFilters(searchState)}
                    filterAttributesInfo={this.props.filterAttributesInfo}
                    groups={this.props.groups}
                    groupName={groupName}
                    indexName={this.props.indexName}
                    isLoadingAttributes={this.props.isLoadingAttributes}
                    listAttributes={this.props.listAttributes}
                    pricing={this.props.pricing}
                    quantity={this.props.quantity}
                    searchTerm={this.props.searchTerm}
                    setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
                    setGroupImage={this.props.setGroupImage}
                    setNbHits={setNbHits}
                    setQuantity={this.props.setQuantity}
                    urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                    valueKey={valueKey}
                    view={this.props.view}
                    viewMoreMinimum={5}
                  />
                );
              }}
            </Group>
          ) : (
            <div>
              <List
                apiUrls={this.props.apiUrls}
                currency={this.props.currency}
                categoryId={this.props.categoryId}
                customerGroupId={this.props.customerGroupId}
                deduplicate={this.props.deduplicate}
                filterAttributesInfo={this.props.filterAttributesInfo}
                groups={this.props.groups}
                isLoadingAttributes={this.props.isLoadingAttributes}
                listAttributes={this.props.listAttributes}
                pricing={this.props.pricing}
                quantity={this.props.quantity}
                setQuantity={this.props.setQuantity}
              />
            </div>
          )}
        </div>
      </React.Fragment>
      </ProductsContextProvider>
    );
  };
};

ListView.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  deduplicate: PropTypes.func.isRequired,
  facetCounts: PropTypes.object.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  groupDescriptions: PropTypes.object.isRequired,
  groupImages: PropTypes.object,
  groupName: PropTypes.string,
  groups: PropTypes.array.isRequired,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  listAttributes: PropTypes.array.isRequired,
  nbHits: PropTypes.object.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  searchState: PropTypes.object,
  searchTerm: PropTypes.string,
  setDefaultGroupQuantity: PropTypes.func,
  setGroupImage: PropTypes.func.isRequired,
  setNbHits: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
  view: PropTypes.string.isRequired,
};

export default ListView;
