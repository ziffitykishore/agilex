import Algolia from '../shared/Algolia';
import ApplySearchTerm from './ApplySearchTerm';
import Breadcrumbs from '../shared/Breadcrumbs';
import DefaultQuantity from './DefaultQuantity';
import DefaultRefinements from '../shared/DefaultRefinements';
import LoadSpotPricing from './LoadSpotPricing';
import NbHitsWithUnits from './NbHitsWithUnits';
import PropTypes from 'prop-types';
import React, { PureComponent } from 'react';
import ResultFacets from '../shared/ResultFacets';
import SearchResultsHeader from '../shared/SearchResultsHeader';
import SearchTopHits from './SearchTopHits';
import Sidebar from './Sidebar';
import ViewMode from './ViewMode';
import ViewSwitcher from './ViewSwitcher';
import VirtualRefinementList from '../shared/VirtualRefinementList';
import classNames from 'classnames';
import { fetchContent } from '../ApiHelpers';
import { renderHitProperty, currentAttributes } from './helpers';
import memoize from 'memoizee';
import qs from 'qs';

const makeFacetLookup = memoize((facets) => {
  let lookup = {};
  for (let facet of facets) {
    lookup[facet.name] = facet.data;
  }
  return lookup;
});

class Products extends PureComponent {
  state = {
    categoryAttributes: {},
    cmsContent: {},
    facetCounts: {},
    groups: [],
    groupImages: {},
    groupDescriptions: {},
    isLoadingAttributes: true,
    isProductInfoShowing: false,
    nbHits: {},
    pricing: [],
    quantity: {},
    shouldTriggerSpotPricing: false,
    view: 'table',
    listAttributes: this.props.defaultListAttributes,
    tableAttributes: this.props.defaultTableAttributes,
    filterAttributesInfo: this.props.defaultFilterAttributesInfo,
  };

  componentDidMount = () => {
    if (this.props.category.product_count === 0) {
      return;
    }

    if (!this.props.categoryId) { // search page
      this.renderSearchInListView();
      this.setState({
        isLoadingAttributes: false,
        tableAttributes: this.props.searchTableAttributes,
        listAttributes: this.props.searchListAttributes,
      });
    }
    else {
      this.getCmsContent();
      this.getGroupDescriptions();
      this.getCategoryAttributes();

      if (Array.isArray(this.props.category.grouping)) {
        this.getGroupValues();
      }
    }
  }

  componentDidUpdate(prevProps) {
    const didGroupingInfoChange = prevProps.category.grouping !== this.props.category.grouping;
    const didCategoryIdChange = prevProps.categoryId !== this.props.categoryId;

    if (didGroupingInfoChange || didCategoryIdChange) {
      this.getGroupValues();
    }

    didCategoryIdChange && this.getCategoryAttributes();
  }

  getGroupValues = (grouping = this.props.category.grouping) => {
    if (!grouping || !grouping.length) {
      this.setState({
        groups: [],
      });

      return;
    }

    this.props.searchClient.initIndex(
      this.props.indexName,
    ).search('', {
      attributesToRetrieve: [],
      facetFilters: [`categoryIds:${this.props.categoryId}`],
      facets: grouping,
      hitsPerPage: 1,
      maxValuesPerFacet: 500,
    }).then(res => {
      const { facets } = res;

      if (!facets) {
        this.setState({
          groups: [],
        });

        return;
      }

      const groups = Object.keys(facets).map(attr => ({
        attribute: attr,
        values: Object.keys(facets[attr]),
      }));

      this.setState({ groups }, this.props.setGroupSearchState);
    })
  }

  addPricing = newPricing => {
    this.setState({
      pricing: [
        ...this.state.pricing,
        ...newPricing,
      ],
    });
  }

  setQuantity = (sku, quantity) => {
    this.setState({
      quantity: {
        ...this.state.quantity,
        [sku]: quantity,
      },
    })
  }

  setDefaultQuantities = payload => {
    // Avoid re-rendering if there's no update
    if (Object.keys(payload).length === 0) {
      return;
    }

    this.setState({
      quantity: {
        ...this.state.quantity,
        ...payload,
      },
    });
  }

  delayedNbHits = [];
  delayedNbHitsTimeout = null;

  setNbHits = (nbHits) => {
    /*
      Travers can select up to 3 grouping attributes. This creates an object
      that keeps track of the number of items per attribute and assigns it a
      very specific key. If we have Size, Color, and Material as the grouping
      attributes for one table in that order:

      Level 1: Size:"2" has 500 products (size::500)
      Level 2: Size:"2" + Color:"Red" has 100 products (size::500--color::100)
      Level 3: Size:"2" + Color:"Red" + "Material:"Metal" has 10 products.
      (size::500--color::100--material::10). These 10 products are the only
      ones we want to show up in the table.
    */
    if (this.delayedNbHitsTimeout === null) {
      this.delayedNbHitsTimeout = setTimeout(() => {
        this.delayedNbHitsTimeout = null;
        this.processNbHits();
      }, 500);
    }
    this.delayedNbHits.push(nbHits);
  }

  processNbHits() {
    const pending = this.delayedNbHits;
    this.delayedNbHits = [];

    this.setState((prevState) => {
      let newValue = prevState.nbHits;

      for (let update of pending) {
        const value = Object.values(update)[0];
        const facets = Object.keys(update)[0].split('--');
        const recursiveFacets = facets.map((facet, i) => {
          if (i > 0) {
            return `${facets[i - 1]}--${facet}`;
          }

          return facet;
        });
        const connectedFacetKeys = recursiveFacets.reduce((hits, facet) => {
          hits[facet] = value;
          return hits;
        }, {});

        newValue = {
          ...newValue,
          ...update,
          ...connectedFacetKeys,
        };
      }

      return {
        nbHits: newValue,
      };
    });
  }

  setView = view => {
    this.setState({
      view,
      isLoadingAttributes: false
    });
  }

  getCmsContent = () => {
    // The catland descriptions and images
    fetchContent(this.props.apiUrls.cms.url, this.props.categoryId).then(cmsContent => {
      this.setState({ cmsContent });
    });
  }

  getCategoryAttributes = () => {
    // How we determine which accordions should show in the refinement list,
    // what the table headers should be, and which details we include in list view.
    fetchContent(this.props.apiUrls.attributes.url, this.props.categoryId).then(categoryAttributes => {
      if (typeof categoryAttributes !== 'object') {
        this.setState({
          isLoadingAttributes: false
        });

        return;
      }

      this.setState({
        filterAttributesInfo: currentAttributes(categoryAttributes.filter_attributes, this.props.defaultFilterAttributesInfo),
        listAttributes: currentAttributes(categoryAttributes.list_attributes, this.props.defaultListAttributes),
        tableAttributes: currentAttributes(categoryAttributes.table_attributes, this.props.defaultTableAttributes)
      }, () => {
        this.setState({
          isLoadingAttributes: false
        });
      });
    });
  }

  getGroupDescriptions = () => {
    fetchContent(this.props.apiUrls.grouping.url, this.props.categoryId).then(groupDescriptions => {
      if (Array.isArray(groupDescriptions) && groupDescriptions.length === 0) {
        groupDescriptions = {};
      }

      this.setState({ groupDescriptions }, this.getGroupValues);
    });
  }

  checkProductInfoView = (isShowing) => {
    this.setState({
      isProductInfoShowing: isShowing,
    });
  }

  setDefaultGroupQuantity = (searchResults) => {
    // Our regular setDefaultQuantities function doesn't work for groups
    // because of the recursion, so we have to modify it a bit
    const newSkus = searchResults.hits.map(hit => {
      return {
        ...hit,
        sku: renderHitProperty(hit.sku),
      };
    }).filter(hit => !(hit.sku in this.state.quantity));

    const quantities = newSkus.reduce((quantities, hit) => {
      quantities[hit.sku] = Math.max(hit.min_sale_qty, hit.qty_increment) || 1;
      return quantities;
    }, {});

    this.setDefaultQuantities(quantities);
  }

  onFacets = (facets) => {
    this.setState({
      facetCounts: makeFacetLookup(facets),
    });
  }

  setGroupImage = imageUrl => {
    this.setState({
      groupImages: {
        ...this.state.groupImages,
        ...imageUrl
      }
    });
  }

  renderSearchInListView = () => {
    if (!this.props.categoryId) {
      this.setState({
        view: 'list'
      });
    }
  }

  triggerSpotPricing = () => {
    this.setState({
      shouldTriggerSpotPricing: true
    });
  }

  render() {
    const searchTerm = qs.parse(window.location.search.slice(1)).search;
    const escapeMedia = html => html && html.replace('{{media url=', `${window.location.origin}/media/`).replace('}}', '');
    const { grouping } = this.props.category;

    const descriptionClasses = classNames({
      description: true,
      'description--has-mobile': this.state.cmsContent.mobile_description,
    });

    const plpContentClasses = classNames({
      'plp-content': true,
      'productInfo-opened': this.state.isProductInfoShowing && this.state.view !== "list",
    });

    const sidebarClasses = classNames({
      'products-sidebar': true,
      'left-panel': true,
      'products-sidebar__opened': this.props.showProductsSidebar,
    });

    if (this.props.category.product_count === 0) {
      return (
        <div>
          <h1 className="no-matches-header">{`We can't find products matching the selection.`}</h1>
        </div>
      )
    }

    return (
      <Algolia
        indexName={this.props.indexName}
        onSearchStateChange={this.props.onSearchStateChange}
        searchState={this.state.searchState}
      >
        <div className="ais-InstantSearch">
          <DefaultRefinements categoryId={this.props.categoryId} />
          <DefaultQuantity
            quantity={this.state.quantity}
            setDefaultQuantities={this.setDefaultQuantities}
          />
          <ApplySearchTerm />
          <LoadSpotPricing
            addPricing={this.addPricing}
            apiUrls={this.props.apiUrls}
            categoryId={this.props.categoryId}
            isLoadingAttributes={this.state.isLoadingAttributes}
            pricing={this.state.pricing}
            shouldTriggerSpotPricing={this.state.shouldTriggerSpotPricing}
          />
          <Breadcrumbs
            categoriesIndexName={this.props.categoriesIndexName}
            category={this.props.category}
            setCategoryId={this.props.setCategoryId}
            showProductsSidebar={this.props.showProductsSidebar}
          />
          <h1 className="hide-on-desktop">{this.props.category.name}</h1>
          <div className={`${descriptionClasses} hide-on-desktop`}>
            <div className="description__desktop" dangerouslySetInnerHTML={{ __html: escapeMedia(this.state.cmsContent.description) }} />
            <div className="description__mobile" dangerouslySetInnerHTML={{ __html: escapeMedia(this.state.cmsContent.mobile_description) }} />
          </div>
          <div className={sidebarClasses}>
            <React.Fragment>
              <div className="hide-button-container">
                <button className="show-hide hide" type="button" onClick={this.props.toggleSidebar}>Hide <span className="arrow left-arrow">❮</span></button>
              </div>
              <Sidebar
                allOrderedAttributes={this.props.allOrderedAttributes}
                filterAttributesInfo={this.state.filterAttributesInfo}
                groups={this.state.groups}
                isLoadingAttributes={this.state.isLoadingAttributes}
                maxVisibleAttributes={this.props.maxVisibleAttributes}
                searchState={this.props.searchState}
                swatchImages={this.props.swatchImages}
                tableAttributes={this.state.tableAttributes}
              />
            </React.Fragment>
          </div>
          {!this.props.showProductsSidebar && (
            <div className="show-button-container">
              <button className="show-hide show" type="button" onClick={this.props.toggleSidebar}>Filters <span className="arrow right-arrow">❯</span></button>
            </div>
          )}
          <div className="right-panel">
            {searchTerm && this.props.categoryId && (
              <h2 className="search-term-header">{`You were redirected from your search for: '${searchTerm}'`}</h2>
            )}
            <h1 className="hide-on-mobile category-title">{this.props.category.name}</h1>
            <div className="plp-top">
              {this.props.category.image_url && (
                <img className="plp-category-image" src={this.props.category.image_url} alt=""></img>
              )}
              <div className={`${descriptionClasses} hide-on-mobile`}>
                <div className="description__desktop" dangerouslySetInnerHTML={{ __html: escapeMedia(this.state.cmsContent.description) }} />
                <div className="description__mobile" dangerouslySetInnerHTML={{ __html: escapeMedia(this.state.cmsContent.mobile_description) }} />
              </div>
            </div>
            <SearchResultsHeader />
            <div className={plpContentClasses}>
              <div className="numHits-view-switcher">
                <ViewSwitcher
                  active={this.state.view}
                  setView={this.setView}
                />
                <div className="number-of-hits">
                  <NbHitsWithUnits />
                </div>
              </div>
              {Array.isArray(grouping) ? grouping.map(group => <VirtualRefinementList key={group} attribute={group} />) : null}
              <ResultFacets onFacets={this.onFacets.bind(this)} />
              <SearchTopHits
                categoryId={this.props.categoryId}
                categoryIndexName={this.props.categoriesIndexName}
                productIndexName={this.props.indexName}
                searchClient={this.props.searchClient}
                triggerSpotPricing={this.triggerSpotPricing}
              />
              <ViewMode
                apiUrls={this.props.apiUrls}
                categoryId={this.props.categoryId}
                checkProductInfoView={this.checkProductInfoView}
                currency={this.props.currency}
                customerGroupId={this.props.customerGroupId}
                deduplicate={this.props.deduplicate}
                facetCounts={this.state.facetCounts}
                filterAttributesInfo={this.state.filterAttributesInfo}
                flyoutAttributes={this.props.flyoutAttributes}
                groups={this.state.groups}
                groupDescriptions={this.state.groupDescriptions}
                groupImages={this.state.groupImages}
                indexName={this.props.indexName}
                isLoadingAttributes={this.state.isLoadingAttributes}
                listAttributes={this.state.listAttributes}
                nbHits={this.state.nbHits}
                pricing={this.state.pricing}
                quantity={this.state.quantity}
                searchState={this.props.searchState}
                searchTerm={searchTerm}
                setNbHits={this.setNbHits}
                selectProduct={this.selectProduct}
                setDefaultGroupQuantity={this.setDefaultGroupQuantity}
                setGroupImage={this.setGroupImage}
                setQuantity={this.setQuantity}
                tableAttributes={this.state.tableAttributes}
                urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                view={this.state.view}
              />
            </div>
          </div>
        </div>
      </Algolia>
    );
  }
}

Products.propTypes = {
  allOrderedAttributes: PropTypes.array.isRequired,
  apiUrls: PropTypes.object.isRequired,
  categoriesIndexName: PropTypes.string.isRequired,
  category: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  deduplicate: PropTypes.func.isRequired,
  defaultFilterAttributesInfo: PropTypes.array.isRequired,
  defaultListAttributes: PropTypes.array.isRequired,
  defaultTableAttributes: PropTypes.array.isRequired,
  flyoutAttributes: PropTypes.array.isRequired,
  hasSpotPricing: PropTypes.bool.isRequired,
  indexName: PropTypes.string.isRequired,
  maxVisibleAttributes: PropTypes.number,
  onSearchStateChange: PropTypes.func.isRequired,
  searchClient: PropTypes.object.isRequired,
  searchState: PropTypes.object.isRequired,
  searchListAttributes: PropTypes.array.isRequired,
  searchTableAttributes: PropTypes.array.isRequired,
  setCategoryId: PropTypes.func.isRequired,
  setGroupSearchState: PropTypes.func,
  showProductsSidebar: PropTypes.bool.isRequired,
  swatchImages: PropTypes.object,
  toggleSidebar: PropTypes.func.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
};

export default Products;
