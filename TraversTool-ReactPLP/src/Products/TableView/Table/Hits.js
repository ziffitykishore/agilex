import React, { useState, useEffect, useContext } from 'react';
import BootstrapTable from 'react-bootstrap-table-next';
import { connectHits } from 'react-instantsearch-dom';
import { getGroupImage, lowestPrice, renderHitProperty, round } from '../../helpers';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import memoize from 'memoizee';
import { ProductsContext } from '../../ProductsContext';

const buildPricingBySku = memoize((pricing) => {
   const hash = {};
   for (let price of pricing) {
      hash[price.sku] = price;
   }
   return hash;
});

const rowClasses = row => {
  return classNames({
    'products-table-rows': true,
    'selected-row': row.isSelected,
  });
};

const defaultSorted = [{
  dataField: 'sku',
  order: 'asc'
}];

const Hits = ({
  currency,
  customerGroupId,
  groupName,
  hits,
  isLoadingAttributes,
  pricing,
  selectProduct,
  selectedProduct,
  setGroupImage,
  setProductsShowingCount,
  setTableExpanded,
  tableAttributes,
  tableExpanded,
  urlToGroupingImagesCatalog
}) => {
  const currencySymbol = currency === 'CAD' ? 'CA $' : '$';
  const sortingArrow = <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="-500, -600 ,1000, 1200"><path d="M100 600v-800l400 400v-300L0-600l-500 500v300l400-400v800z" /></svg>
  const {products,setProducts} = useContext(ProductsContext);
  useEffect(() => {
    const groupImage = getGroupImage({ hits, urlToGroupingImagesCatalog });

    if (setGroupImage && groupName) {
      setGroupImage({
        [groupName]: groupImage
      })
    }
    if(hits.length && JSON.stringify(products[groupName]) !== JSON.stringify(hits)){
      setProducts({...products,[groupName]:hits});
    }
    setProductsShowingCount(hits.length);
  }, [hits])

  const defaultColumnProperties = column => {
    return ({
      dataField: column.id,
      text: column.label,
      sort: true,
      headerEvents: {
        onClick: () => {
          setTableExpanded(true);
        }
      },
      /* eslint react/display-name: 0 */
      // `yarn build` thinks sortCaret is a component. The line above overrides that
      sortCaret: (order, column) => {
        if (!order || order === 'asc') return (<span className="sorting-arrow">{sortingArrow}</span>);
        else if (order === 'desc') return (<span className="sorting-arrow down-arrow">{sortingArrow}</span>);
        return null;
      },
    });
  }

  const columns = tableAttributes.slice(0, 9).map(column => {
    // Ensure price is always the last column in the table
    if (column.id === "price") {
      return ({
        ...defaultColumnProperties(column),
        sortFunc: (a, b, order) => {
          a = a.replace(currencySymbol, '');
          b = b.replace(currencySymbol, '');

          return order === 'asc' ? b - a : a - b;
        }
      });
    } else {
      return defaultColumnProperties(column);
    }
  })

  const selectRow = {
    mode: 'radio',
    clickToSelect: true,
    class: 'selected-row',
    hideSelectColumn: true,
    onSelect: (row) => {
      selectProduct(row);
    }
  };

  const pricingBySku = buildPricingBySku(pricing);
  const results = hits.filter(hit => hit.in_stock).map(hit => {
    const sku = renderHitProperty(hit.sku);
    const spotPrice = pricingBySku[sku] || {};
    const price = lowestPrice(currency, hit, customerGroupId, spotPrice);

    return {
      ...hit,
      sku,
      name: hit.name,
      finish_coating: renderHitProperty(hit.color),
      isSelected: hit.objectID === selectedProduct.objectID,
      msrp: hit.manufacturer_price,
      price: currencySymbol + round(price),
      defaultPrice: hit.price[currency],
      tierGroups: hit[`group_${customerGroupId}_tiers`],
      type_id: hit.type_id,
      url: hit.url,
    }
  });

  const columnsToShow = () => {
    // We don't want to show a table attribute if none of the items in the table have that attribute,
    // so we only show columns that are in tableAttributes and have products that have that attribute
    let attributesWithData = [];
    let columnsToShow = [];

    results.map(result => {
      columns.map(column => {
        if (result[column.dataField]) {
          if (attributesWithData.indexOf(column.dataField) === -1) {
            attributesWithData.push(column.dataField);
            return attributesWithData;
          }
        }
      });
    });

    columns.map(column => {
      if (attributesWithData.indexOf(column.dataField) !== -1) {
        columnsToShow.push(column);
      }
    });

    return columnsToShow;
  }

  const tableClasses = classNames({
    'products-table': true,
    [`header-count-${columns.length}`]: true,
    'sticky': tableExpanded,
  })

  return (
    hits.length > 0 && (
      <React.Fragment>
        <BootstrapTable
          keyField='sku'
          data={results}
          columns={columnsToShow()}
          selectRow={selectRow}
          hover={true}
          classes={tableClasses}
          rowClasses={rowClasses}
          defaultSorted={defaultSorted}
        />
      </React.Fragment>
    )
  );
};

Hits.propTypes = {
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  groupName: PropTypes.string,
  hits: PropTypes.array.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  pricing: PropTypes.array.isRequired,
  selectProduct: PropTypes.func.isRequired,
  setGroupImage: PropTypes.func,
  setProductsShowingCount: PropTypes.func.isRequired,
  setTableExpanded: PropTypes.func.isRequired,
  tableAttributes: PropTypes.array.isRequired,
  tableExpanded: PropTypes.bool.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string,
};

export default connectHits(Hits);
