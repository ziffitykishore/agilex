import qs from 'qs';
import memoize from 'memoizee';

// Takes configurable and simple products into account
export function renderHitProperty(hitProperty) {
  return Array.isArray(hitProperty) ? hitProperty[0] : hitProperty;
}

export function createURL(state) {
  window.qs = qs;
  const query = qs.stringify(state.refinementList);
  return query === '' ? '' : `?${query}`;
};

export function searchStateToUrl(props, searchState) {
  let selectedRefinements = searchState.refinementList;
  const searchObject = qs.parse(window.location.search.slice(1));
  const searchTerm = searchObject.q;
  const searchTermCat = searchObject.search;
  const state = { refinementList: { ...searchState.refinementList }};
  const refinementParams = `${props.location.pathname}${createURL(state)}`;

  if (selectedRefinements) {
    Object.keys(selectedRefinements).forEach(key => {
      if (selectedRefinements[key] === '') {
        delete state.refinementList[key];
      }
    });

    const baseURL = `${props.location.pathname}${createURL(state)}`;
    if (searchTerm) {
      return (baseURL.indexOf('?') < 0 ? '?' : `${baseURL}&`) + `q=${searchTerm}`;
    }

    return baseURL;
  }

  if (searchTerm) {
    return refinementParams.indexOf('?') >= 0 ? `${refinementParams}&q=${searchTerm}` : `${refinementParams}?q=${searchTerm}`;
  } else {
    return refinementParams;
  }
}

const searchToRefinementList = memoize((search) => {
  return qs.parse(search);
});

export function urlToSearchState(location) {
  const routeState = searchToRefinementList(location.search.slice(1));
  const searchState = {
    refinementList: routeState,
  };

  return searchState;
};

export const lowestPrice = memoize((currency, hit, customerGroupId, spotPrice) => {
  const { default: defaultPrice } = hit.price[currency];
  const exactPrices = [hit.exact_unit_price, hit.special_exact_unit_price];
  const prices = [defaultPrice, spotPrice.price, ...exactPrices];
  const minPrice = Math.min.apply(null, prices.filter(Boolean));

  try {
    const groupTier = hit[`group_${customerGroupId}_tiers`];

    return JSON.parse(groupTier).map(tier => tier.price).reduce(
      (a, b) => Math.min(a, b),
      minPrice
    );
  }
  catch (e) {
    return minPrice;
  }
});

export function currentAttributes(categoryAttributes, defaultAttributes,changeOrder) {
  if (!categoryAttributes) {
    return defaultAttributes;
  }

  let orderedCategoryAttributes = [];
  let categoryAttributesArray = typeof categoryAttributes === 'object' ? Object.values(categoryAttributes) : categoryAttributes;

  categoryAttributesArray.map(categoryAttribute => {
    const item = defaultAttributes.find(({id}) => categoryAttribute === id);
    item && orderedCategoryAttributes.push(item);
  });
  
  if(changeOrder){
    const index =  orderedCategoryAttributes.findIndex(data=>data.id === "shopby");
    if(index > 0){
      const shopByCategory = {...orderedCategoryAttributes[index]};
      orderedCategoryAttributes.splice(index,1);
      const newOrderedCategory = [shopByCategory].concat(orderedCategoryAttributes);
      return newOrderedCategory;
    }
  }
  
  return orderedCategoryAttributes;
}

export function formatPrice(price) {
  return price.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

export const round = (number) => ((number * 100) / 100).toFixed(2);

/*
  We are using facetFilters to combine grouping attributes with filter attributes from the PLP sidebar.
  Algolia requires a specific syntax to differentiate AND vs OR for filtering. This function
  ensures that if 2+ filters are selected from the same category, the search is treated as 'OR' instead of 'AND',
  as AND will return no results (you're not going to have a single screw that is both 3.5" AND 1.5")

  See https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/#and-and-or-filter-combination
*/
export const facetFilters = searchState => Object.values(searchState).map((value, index) => {
  let filters = [];
  const key = Object.keys(searchState)[index];

  if (key !== 'SID' && key !== 'search') {
    if (Array.isArray(value)) {
      value.map(splitValue => {
        filters.push(`${key}: ${splitValue}`);
      });
    } else {
      filters.push(`${key}: ${value}`);
    }
  }

  return filters;
});

export const getGroupImage = ({ urlToGroupingImagesCatalog, hits }) => {
  if (!urlToGroupingImagesCatalog || !hits.length) {
    return;
  }

  if (hits[0] && hits[0].item_group_image) {
    return urlToGroupingImagesCatalog + hits[0].item_group_image + '.jpg';
  }
};
