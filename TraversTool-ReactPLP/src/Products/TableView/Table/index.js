import React, { useState, useRef } from 'react';
import { connectMenu } from 'react-instantsearch-dom';
import TableHits from './Hits';
import ViewAll from '../../ViewAll';
import PropTypes from 'prop-types';

const VirtualMenu = connectMenu(() => null);

const SelectGroup = ({
  attribute,
  value,
}) => (
  <VirtualMenu attribute={attribute} defaultRefinement={value} />
);

export default function Table({
  currency,
  customerGroupId,
  filterAttributesInfo,
  groupName,
  isLoadingAttributes,
  pricing,
  selectProduct,
  selectedProduct,
  setGroupImage,
  tableAttributes,
  urlToGroupingImagesCatalog,
  value,
  viewMoreMinimum
}) {
  const defaultProductsShowingCount = 15;
  const [tableExpanded, setTableExpanded] = useState(false);
  const [productsShowingCount, setProductsShowingCount] = useState(defaultProductsShowingCount);

  const tableRef = React.createRef();

  return (
    <div ref={tableRef}>
      <SelectGroup
        attribute={filterAttributesInfo.map(attribute => attribute.id)[0]}
        value={value}
      />
      <TableHits
        currency={currency}
        customerGroupId={customerGroupId}
        groupName={groupName}
        isLoadingAttributes={isLoadingAttributes}
        pricing={pricing}
        selectProduct={selectProduct}
        selectedProduct={selectedProduct}
        setGroupImage={setGroupImage}
        setProductsShowingCount={setProductsShowingCount}
        setTableExpanded={setTableExpanded}
        tableAttributes={tableAttributes}
        tableExpanded={tableExpanded}
        urlToGroupingImagesCatalog={urlToGroupingImagesCatalog}
      />
      <div className="view-all">
        <ViewAll
          min={defaultProductsShowingCount}
          productsShowingCount={productsShowingCount}
          setTableExpanded={setTableExpanded}
          tableExpanded={tableExpanded}
        />
      </div>
    </div>
  );
};


Table.propTypes = {
  attribute: PropTypes.string,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  filterAttributesInfo: PropTypes.array.isRequired,
  groupName: PropTypes.string,
  isLoadingAttributes: PropTypes.bool.isRequired,
  pricing: PropTypes.array.isRequired,
  selectProduct: PropTypes.func.isRequired,
  selectedProduct: PropTypes.object,
  setGroupImage: PropTypes.func,
  tableAttributes: PropTypes.array.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string,
  value: PropTypes.string,
  viewMoreMinimum: PropTypes.number,
};

SelectGroup.propTypes = {
  attribute: PropTypes.string,
  value: PropTypes.string,
};
