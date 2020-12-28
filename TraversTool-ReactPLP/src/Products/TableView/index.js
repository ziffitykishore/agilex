import Algolia from '../../shared/Algolia';
import Group from '../Group';
import GroupedTableHolder from './Table/GroupedTableHolder';
import ProductInfo from './ProductInfo';
import React, { PureComponent } from 'react';
import Table from './Table';
import PropTypes from 'prop-types';
import { debouncedForceCheck } from '../debouncedForceCheck';

export default class TableView extends PureComponent {
  state = {
    selectedProduct: {},
  }

  componentDidUpdate = (prevProps) => {
    if (prevProps.searchState.refinementList !== this.props.searchState.refinementList) {
      debouncedForceCheck();
    }
  }

  selectProduct = (hit) => {
    this.setState({
      selectedProduct: hit,
    }, this.props.checkProductInfoView(true));
  }

  render() {
    return (
      <React.Fragment>
        <div className="table-and-flyout">
          {this.props.groups.length ? (
            <Group
              descriptions={this.props.groupDescriptions}
              facetCounts={this.props.facetCounts}
              groupImages={this.props.groupImages}
              groups={this.props.groups}
              nbHits={this.props.nbHits}
              searchState={this.props.searchState.refinementList}
              setNbHits={this.props.setNbHits}
              view={this.props.view}
            >
              {({ groupName, refinements: groupRefinements, setNbHits, valueKey }) => {
                return (
                  <div className="table-view">
                    <GroupedTableHolder
                      categoryId={this.props.categoryId}
                      currency={this.props.currency}
                      customerGroupId={this.props.customerGroupId}
                      facetCounts={this.props.facetCounts}
                      filterAttributesInfo={this.props.filterAttributesInfo}
                      groupName={groupName}
                      groupRefinements={groupRefinements}
                      indexName={this.props.indexName}
                      isLoadingAttributes={this.props.isLoadingAttributes}
                      pricing={this.props.pricing}
                      searchState={this.props.searchState}
                      searchTerm={this.props.searchTerm}
                      selectProduct={this.selectProduct}
                      selectedProduct={this.state.selectedProduct}
                      setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
                      setGroupImage={this.props.setGroupImage}
                      setNbHits={setNbHits}
                      tableAttributes={this.props.tableAttributes}
                      urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                      valueKey={valueKey}
                      view={this.props.view}
                      viewMoreMinimum={15}
                    />
                  </div>
                );
              }}
            </Group>
          ) : (
            <div className="table-view">
              <Table
                currency={this.props.currency}
                customerGroupId={this.props.customerGroupId}
                filterAttributesInfo={this.props.filterAttributesInfo}
                isLoadingAttributes={this.props.isLoadingAttributes}
                pricing={this.props.pricing}
                selectProduct={this.selectProduct}
                selectedProduct={this.state.selectedProduct}
                tableAttributes={this.props.tableAttributes}
              />
            </div>
          )}
          <ProductInfo
            apiUrls={this.props.apiUrls}
            currency={this.props.currency}
            customerGroupId={this.props.customerGroupId}
            checkProductInfoView={this.props.checkProductInfoView}
            flyoutAttributes={this.props.flyoutAttributes}
            hit={this.state.selectedProduct}
            pricing={this.props.pricing}
            quantity={this.props.quantity}
            selectProduct={this.selectProduct}
            setQuantity={this.props.setQuantity}
          />
        </div>
      </React.Fragment>
    );
  }
};

TableView.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  currency: PropTypes.string.isRequired,
  checkProductInfoView: PropTypes.func.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  facetCounts: PropTypes.object.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  flyoutAttributes: PropTypes.array.isRequired,
  groups: PropTypes.array.isRequired,
  groupDescriptions: PropTypes.object.isRequired,
  groupImages: PropTypes.object.isRequired,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  nbHits: PropTypes.object.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  searchState: PropTypes.object.isRequired,
  searchTerm: PropTypes.string,
  setNbHits: PropTypes.func.isRequired,
  setDefaultGroupQuantity: PropTypes.func,
  setGroupImage: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired,
  tableAttributes: PropTypes.array.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
  view: PropTypes.string.isRequired,
};
