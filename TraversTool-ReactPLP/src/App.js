import Algolia from './shared/Algolia';
import ApplySearchTerm from "./Products/ApplySearchTerm";
import Categories from './Categories';
import VirtualMenu from './shared/DefaultRefinements/VirtualMenu';
import HasResults from './shared/HasResults';
import Products from './Products';
import React, { Component } from 'react';
import SearchClient from './shared/SearchClient';
import SearchTopHits from './Products/SearchTopHits';
import algoliasearch from 'algoliasearch';
import classNames from 'classnames';
import memoize from 'memoizee';
import { fetchContent } from './ApiHelpers';
import { withRouter } from 'react-router-dom';
import { searchStateToUrl, urlToSearchState } from './Products/helpers';
import PropTypes from 'prop-types';
import qs from 'qs';

const memoUrlToSearchState = memoize((url, prevState) => {
  return {
    ...prevState,
    ...urlToSearchState(url),
  };
}, { max: 32 });

class App extends Component {
  state = {
    allOrderedAttributes: [],
    category: {},
    categoryId: this.props.defaultCategoryId,
    hasSpotPricing: this.props.hasSpotPricing,
    isStaticBlockPage: false,
    lastLocation: this.props.location,
    searchHits: null,
    searchState: memoUrlToSearchState(this.props.location, {}),
    showProductsSidebar: true,
    staticBlockContent: null,
  }

  searchClient = algoliasearch(
    this.props.algolia.applicationId,
    this.props.algolia.searchApiKey,
  )

  componentDidMount() {
    this.setState({ initialLocation: this.props.location });
    this.checkForSearchResults();

    // Allows the user to use back/forward buttons to navigate between categories
    this.props.history.listen(location => {
      if (location.state && location.state.categoryId) {
        const { categoryId } = location.state;
        this.setState({
          categoryId
        }, this.getCategory);
      }
    });
    this.getCategory();
    this.getAttributes();
    this.isStaticBlockPage();
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.categoryId !== this.state.categoryId) {
      // Retrieve the new category's info and attribute value sort order.
      this.getCategory();
      this.getAttributes();
    }
  }

  checkForSearchResults = () => {
    // This searches the products index for the search term. We only
    // care that we get a single hit; we don't need to know all of the search hits
    const searchTerm = qs.parse(window.location.search.slice(1)).q;

    if (!this.state.categoryId) {
      this.searchClient.initIndex(
        this.props.algolia.productsIndexName
      ).search(searchTerm, {
        maxValuesPerFacet: 1,
        attributesToRetrieve: ['sku'],
      }).then((res) => {
        this.setState({
          searchHits: res.hits
        });
      });
    }
  }

  replaceHistory = () => {
    this.props.history.replace({ state: { categoryId: this.state.categoryId } });
  }

  uniqBy = (arr, predicate) => {
    const cb = typeof predicate === 'function' ? predicate : (o) => o[predicate];
    return [...arr.reduce((map, item) => {
      const key = cb(item);
      map.has(key) || map.set(key, item);
      return map;
    }, new Map()).values()];
  };

  deduplicate = items => {
    return this.uniqBy(items, item => item.attribute);
  }

  toggleSidebar = () => {
    this.setState(prevState => ({
      showProductsSidebar: !prevState.showProductsSidebar,
    }));
  }

  setCategoryId = ({ hit }) => {
    // Set the categoryId and update the url to reflect the current category
    const { category_id: categoryId, url } = hit;
    const categoryName = url.match(/([^\/]*)\/*$/)[1];
    const cleanUrl = `${window.location.origin}/category/${categoryName}`;

    this.setState({
      categoryId
    });
    this.props.history.push(
      cleanUrl.replace(/^.*\/\/[^/]+/, ''),
      { categoryId }
    );
    window.scrollTo(0, 0);
  }

  getCategory = () => {
    // We won't have a categoryId on the search results page
    if (!this.state.categoryId) {
      return;
    }

    this.searchClient.initIndex(
      this.props.algolia.categoriesIndexName
    ).getObject(this.state.categoryId).then(category => {
      document.title = category.name;
      const metaCanonical = document.querySelector('link[rel="canonical"]');
      if (metaCanonical) {
        metaCanonical.href = category.url;
      }
      this.setState({
        category,
      });
    });
  }

  getAttributes = () => {
    if (!this.props.apiUrls.facetAttributes) {
      return;
    }
    const id = this.state.categoryId || 'search';
    fetchContent(this.props.apiUrls.facetAttributes.url, id).then(allOrderedAttributes => {
      this.setState({ allOrderedAttributes });
    });
  }

  static getDerivedStateFromProps(props, state) {
    // Syncs the refinement list (sidebar) with the url
    if (props.location !== state.lastLocation) {
      return {
        searchState: memoUrlToSearchState(props.location, state.searchState),
        lastLocation: props.location,
      };
    }
    return null;
  }

  onSearchStateChange = (searchState) => {
    this.props.history.replace(
      searchStateToUrl(this.props, searchState),
      searchState,
    );
    this.setState({ searchState });
  };

  isStaticBlockPage = () => {
    if (this.props.displayMode === "PAGE") {
      this.setState({
        isStaticBlockPage: true
      }, () => {
        fetchContent(this.props.apiUrls.cms.url, this.state.categoryId).then(staticBlockContent => {
          this.setState({
            staticBlockContent,
          });
        });
      });
    }
  }

  setGroupSearchState = () => {
    this.setState(state => ({
      searchState: memoUrlToSearchState(state.initialLocation, state.searchState),
    }));
    this.onSearchStateChange(this.state.searchState)
  }

  hasStaticContent = () => {
    if (this.state.isStaticBlockPage && this.state.staticBlockContent !== null) {
      return true;
    }
  }

  render() {
    const searchTerm = qs.parse(window.location.search.slice(1)).q;

    if (this.hasStaticContent()) {
      const content = this.state.staticBlockContent;
      const staticBlockClasses = classNames({
        'static-block': true,
        'has-mobile': content.mobile_description && content.mobile_description.length,
      });

      return (
        <div className={staticBlockClasses}>
          <div className="static-block-desktop" dangerouslySetInnerHTML={{ __html: content.description }} />
          <div className="static-block-mobile" dangerouslySetInnerHTML={{ __html: content.mobile_description }} />
        </div>
      );
    }

    return (
      <SearchClient.Provider value={this.searchClient}>
        <Algolia indexName={this.props.algolia.categoriesIndexName}>
          {this.state.categoryId ? ( // Category Landing Page / Product Listing Page
            <React.Fragment>
              <VirtualMenu attribute="parent_category_id" defaultRefinement={this.state.categoryId.toString()} />
              <HasResults
                noResults={(
                  <div className="product-landing-page">
                    <Products
                      allOrderedAttributes={this.state.allOrderedAttributes}
                      apiUrls={this.props.apiUrls}
                      categoriesIndexName={this.props.algolia.categoriesIndexName}
                      category={this.state.category}
                      categoryId={this.state.categoryId}
                      currency={this.props.currency}
                      customerGroupId={this.props.customerGroupId}
                      deduplicate={this.deduplicate}
                      defaultFilterAttributesInfo={this.props.defaultFilterAttributesInfo}
                      defaultListAttributes={this.props.defaultListAttributes}
                      defaultTableAttributes={this.props.defaultTableAttributes}
                      flyoutAttributes={this.props.flyoutAttributes}
                      hasSpotPricing={this.state.hasSpotPricing}
                      indexName={this.props.algolia.productsIndexName}
                      maxVisibleAttributes={this.props.attributesValuesLimit}
                      onSearchStateChange={this.onSearchStateChange}
                      searchClient={this.searchClient}
                      searchListAttributes={this.props.searchListAttributes}
                      searchTableAttributes={this.props.searchTableAttributes}
                      searchState={this.state.searchState}
                      searchStateToUrl={searchStateToUrl}
                      setCategoryId={this.setCategoryId}
                      setGroupSearchState={this.setGroupSearchState}
                      showProductsSidebar={this.state.showProductsSidebar}
                      swatchImages={this.props.swatchImages}
                      toggleSidebar={this.toggleSidebar}
                      urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                    />
                  </div>
                )}
                results={(
                  <div className="category-landing-page">
                    <Categories
                      apiUrls={this.props.apiUrls}
                      category={this.state.category}
                      categoryId={this.state.categoryId}
                      currency={this.props.currency}
                      indexName={this.props.algolia.categoriesIndexName}
                      replaceHistory={this.replaceHistory}
                      setCategoryId={this.setCategoryId}
                    />
                  </div>
                )}
              />
            </React.Fragment>
          ) : ( // Search results page (which is treated just like a PLP)
            <React.Fragment>
              <ApplySearchTerm />
              {this.state.searchHits !== null ? (
                !this.state.searchHits.length ? (
                  <h1 className="search-results-header no-search-results">
                    {`There are no results for: ${searchTerm}`}
                  </h1>
                ) : (
                  <div className="product-landing-page">
                    <Products
                      allOrderedAttributes={this.state.allOrderedAttributes}
                      apiUrls={this.props.apiUrls}
                      categoriesIndexName={this.props.algolia.categoriesIndexName}
                      category={this.state.category}
                      categoryId={this.state.categoryId}
                      currency={this.props.currency}
                      customerGroupId={this.props.customerGroupId}
                      deduplicate={this.deduplicate}
                      defaultFilterAttributesInfo={this.props.defaultFilterAttributesInfo}
                      defaultListAttributes={this.props.defaultListAttributes}
                      defaultTableAttributes={this.props.defaultTableAttributes}
                      flyoutAttributes={this.props.flyoutAttributes}
                      hasSpotPricing={this.state.hasSpotPricing}
                      indexName={this.props.algolia.searchIndexName}
                      maxVisibleAttributes={this.props.attributesValuesLimit}
                      onSearchStateChange={this.onSearchStateChange}
                      searchClient={this.searchClient}
                      searchState={this.state.searchState}
                      searchStateToUrl={searchStateToUrl}
                      searchListAttributes={this.props.searchListAttributes}
                      searchTableAttributes={this.props.searchTableAttributes}
                      setCategoryId={this.setCategoryId}
                      showProductsSidebar={this.state.showProductsSidebar}
                      swatchImages={this.props.swatchImages}
                      toggleSidebar={this.toggleSidebar}
                      urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
                    />
                  </div>
                )
              ) : (
                null
              )}
            </React.Fragment>
          )}
        </Algolia>
      </SearchClient.Provider>
    );
  }
}

App.propTypes = {
  algolia: PropTypes.object.isRequired,
  apiUrls: PropTypes.object.isRequired,
  attributesValuesLimit: PropTypes.number,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  defaultCategoryId: PropTypes.number,
  defaultFilterAttributesInfo: PropTypes.array.isRequired,
  defaultListAttributes: PropTypes.array.isRequired,
  defaultTableAttributes: PropTypes.array.isRequired,
  displayMode: PropTypes.string,
  flyoutAttributes: PropTypes.array.isRequired,
  hasSpotPricing: PropTypes.bool.isRequired,
  history: PropTypes.object.isRequired,
  location: PropTypes.object.isRequired,
  searchListAttributes: PropTypes.array.isRequired,
  searchTableAttributes: PropTypes.array.isRequired,
  swatchImages: PropTypes.object,
  urlToGroupingImagesCatalog: PropTypes.string.isRequired,
};

export default withRouter(App);
