import ListView from './ListView';
import Media from 'react-media';
import React, { PureComponent } from 'react';
import TableView from './TableView';
import PropTypes from 'prop-types';

class ViewMode extends PureComponent {
  render() {
    const { view } = this.props;

    return (
      <React.Fragment>
        <Media query="(min-width: 1200px)">
          {matches => matches ? (
            <React.Fragment>
              {view === 'list' ? (
                <ListView
                  apiUrls={this.props.apiUrls}
                  categoryId={this.props.categoryId}
                  currency={this.props.currency}
                  customerGroupId={this.props.customerGroupId}
                  deduplicate={this.props.deduplicate}
                  facetCounts={this.props.facetCounts}
                  filterAttributesInfo={this.props.filterAttributesInfo}
                  groupDescriptions={this.props.groupDescriptions}
                  groupImages={this.props.groupImages}
                  groups={this.props.groups}
                  indexName={this.props.indexName}
                  isLoadingAttributes={this.props.isLoadingAttributes}
                  listAttributes={this.props.listAttributes}
                  nbHits={this.props.nbHits}
                  pricing={this.props.pricing}
                  quantity={this.props.quantity}
                  searchState={this.props.searchState}
                  searchTerm={this.props.searchTerm}
                  setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
                  setGroupImage={this.props.setGroupImage}
                  setNbHits={this.props.setNbHits}
                  setQuantity={this.props.setQuantity}
                  urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                  view={this.props.view}
                />
              ) : (
                <TableView
                  apiUrls={this.props.apiUrls}
                  categoryId={this.props.categoryId}
                  currency={this.props.currency}
                  customerGroupId={this.props.customerGroupId}
                  checkProductInfoView={this.props.checkProductInfoView}
                  facetCounts={this.props.facetCounts}
                  filterAttributesInfo={this.props.filterAttributesInfo}
                  flyoutAttributes={this.props.flyoutAttributes}
                  groupDescriptions={this.props.groupDescriptions}
                  groupImages={this.props.groupImages}
                  groups={this.props.groups}
                  indexName={this.props.indexName}
                  isLoadingAttributes={this.props.isLoadingAttributes}
                  nbHits={this.props.nbHits}
                  pricing={this.props.pricing}
                  quantity={this.props.quantity}
                  searchState={this.props.searchState}
                  searchTerm={this.props.searchTerm}
                  setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
                  setGroupImage={this.props.setGroupImage}
                  setNbHits={this.props.setNbHits}
                  setQuantity={this.props.setQuantity}
                  tableAttributes={this.props.tableAttributes}
                  urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                  view={this.props.view}
                />
              )}
            </React.Fragment>
          ) : (
            <ListView
              apiUrls={this.props.apiUrls}
              categoryId={this.props.categoryId}
              currency={this.props.currency}
              customerGroupId={this.props.customerGroupId}
              deduplicate={this.props.deduplicate}
              facetCounts={this.props.facetCounts}
              filterAttributesInfo={this.props.filterAttributesInfo}
              groupDescriptions={this.props.groupDescriptions}
              groupImages={this.props.groupImages}
              groups={this.props.groups}
              indexName={this.props.indexName}
              isLoadingAttributes={this.props.isLoadingAttributes}
              listAttributes={this.props.listAttributes}
              nbHits={this.props.nbHits}
              pricing={this.props.pricing}
              quantity={this.props.quantity}
              searchState={this.props.searchState}
              searchTerm={this.props.searchTerm}
              setDefaultGroupQuantity={this.props.setDefaultGroupQuantity}
              setGroupImage={this.props.setGroupImage}
              setNbHits={this.props.setNbHits}
              setQuantity={this.props.setQuantity}
              urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
              view={this.props.view}
            />
          )}
        </Media>
      </React.Fragment>
    );
  }
}

ViewMode.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  deduplicate: PropTypes.func.isRequired,
  checkProductInfoView: PropTypes.func.isRequired,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  facetCounts: PropTypes.object.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  flyoutAttributes: PropTypes.array.isRequired,
  groupDescriptions: PropTypes.object.isRequired,
  groupImages: PropTypes.object.isRequired,
  groups: PropTypes.array.isRequired,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  listAttributes: PropTypes.array.isRequired,
  nbHits: PropTypes.object.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  setNbHits: PropTypes.func.isRequired,
  searchState: PropTypes.object.isRequired,
  searchTerm: PropTypes.string,
  setDefaultGroupQuantity: PropTypes.func.isRequired,
  setGroupImage: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired,
  tableAttributes: PropTypes.array.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
  view: PropTypes.string.isRequired,
};

export default ViewMode;
