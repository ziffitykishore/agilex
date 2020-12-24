import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import qs from 'qs';

class SearchTopHits extends PureComponent {
  state = {
    allCategories: [],
    cleanedMatchingCategories: {},
    matchingCategories: [],
    searchTerm: qs.parse(window.location.search.slice(1)).q,
    sortedMatchingCategories: []
  }

  getAllCategories = () => {
    let hits = [];

    this.props.searchClient.initIndex(
      this.props.categoryIndexName
    ).browseObjects({
      batch: objects => (hits = hits.concat(objects)),
    }).then(() => {
      this.setState({
        allCategories: hits,
      });
      this.props.triggerSpotPricing();
    });
  }

  getMatchingCategories = () => {
    this.props.searchClient.initIndex(
      this.props.productIndexName
    ).search(this.state.searchTerm, {
      maxValuesPerFacet: 100,
      attributesToRetrieve: '*',
      facets: '*'
    }).then((res) => {
      this.setState({
        matchingCategories: res.facets['categories.level3'] || [],
      });
    });
  }

  componentDidMount = () => {
    this.getAllCategories();
    this.getMatchingCategories();
  }

  componentDidUpdate = (prevProps, prevState) => {
    if (prevState.matchingCategories !== this.state.matchingCategories) {
      this.cleanUpMatchingCategories();
    }

    if (prevState.allCategories !== this.state.allCategories) {
      this.combineCategoryInformation();
    }
  }

  cleanUpMatchingCategories = () => {
    let cleanedMatchingCategories = {};

    Object.keys(this.state.matchingCategories).map(category => {
      cleanedMatchingCategories[category.split('/// ').pop()] = this.state.matchingCategories[category];
    });

    this.setState({ cleanedMatchingCategories });
  }

  combineCategoryInformation = () => {
    let allMatchingCategories = {};
    let sortedMatchingCategories = [];

    if (!this.state.allCategories) {
      return;
    }

    this.state.allCategories.forEach(category => {
      const { name, url, image_url } = category;

      if (this.state.cleanedMatchingCategories[name]) {
        allMatchingCategories[name] = {
          name, url, image_url, count: this.state.cleanedMatchingCategories[name]
        };
      }
    });

    for (let category in allMatchingCategories) {
      sortedMatchingCategories.push(allMatchingCategories[category]);
    }

    sortedMatchingCategories.sort((a, b) => b.count - a.count);

    this.setState({ sortedMatchingCategories });
  }

  render() {
    if (!this.state.sortedMatchingCategories.length) {
      return null;
    }

    return (
      !this.props.categoryId && (
        <div className="search-top-hits">
          <p>Narrow your search by category</p>
          <div className="search-top-hits-buttons">
            {this.state.sortedMatchingCategories.slice(0, 12).map(category => (
              <a
                href={`${category.url}?search=${this.state.searchTerm}`}
                key={category.name}
                className="search-hit"
              >
                {category.image_url && <img src={category.image_url} alt=""></img>}
                <span className="search-hit-name">{category.name}</span>
                <span className="search-hit-count">({category.count})</span>
              </a>
            ))}
          </div>
        </div>
      )
    )
  }
}

SearchTopHits.propTypes = {
  categoryId: PropTypes.number,
  categoryIndexName: PropTypes.string.isRequired,
  productIndexName: PropTypes.string.isRequired,
  searchClient: PropTypes.object.isRequired,
  triggerSpotPricing: PropTypes.func.isRequired,
};

export default SearchTopHits;
