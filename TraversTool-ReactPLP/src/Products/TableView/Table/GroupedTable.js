import Algolia from '../../../shared/Algolia';
import React, { PureComponent } from 'react';
import Table from '../Table';
import PropTypes from 'prop-types';
import { Configure } from 'react-instantsearch-dom';
import SetHitCount from '../../SetHitCount';
import VirtualSearchBox from '../../../shared/DefaultRefinements/VirtualSearchBox';

class GroupedTable extends PureComponent {
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
          <Table
            currency={this.props.currency}
            customerGroupId={this.props.customerGroupId}
            filterAttributesInfo={this.props.filterAttributesInfo}
            groupName={this.props.groupName}
            isLoadingAttributes={this.props.isLoadingAttributes}
            pricing={this.props.pricing}
            selectProduct={this.props.selectProduct}
            selectedProduct={this.props.selectedProduct}
            setGroupImage={this.props.setGroupImage}
            tableAttributes={this.props.tableAttributes}
            urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
            viewMoreMinimum={this.props.viewMoreMinimum}
          />
        </Algolia>
      </React.Fragment>
    );
  }
};

GroupedTable.propTypes = {
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  facetFilters: PropTypes.array.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  groupName: PropTypes.string,
  indexName: PropTypes.string.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  pricing: PropTypes.array.isRequired,
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

export default GroupedTable;
