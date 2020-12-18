import Breadcrumbs from '../shared/Breadcrumbs';
import Header from './Header';
import { Hits, Configure } from 'react-instantsearch-dom';
import MediaCollapsible from '../Products/MediaCollapsible';
import PropTypes from 'prop-types';
import React, { PureComponent } from 'react';
import SiblingCategories from './SiblingCategories';
import { fetchContent } from '../ApiHelpers';

class Categories extends PureComponent {
  state = {
    category: {},
    cmsContent: {
      after_content: '',
      after_sidebar: '',
      description: '',
      mobile_description: '',
    },
    shouldOpenCollapsible: false
  }

  componentDidMount = () => {
    this.mounted = true;
    this.getCmsContent();
    // Fixes forward/back button not working on the first category on which you land
    this.props.replaceHistory();
  }

  componentDidUpdate(prevProps, prevState) {
    if (this.props.categoryId !== prevProps.categoryId) {
      this.getCmsContent();
    }
  }

  getCmsContent = () => {
    fetchContent(this.props.apiUrls.cms.url, this.props.categoryId).then(cmsContent => {
      if (this.mounted) {
        this.setState({ cmsContent });
      }
    });
  }

  componentWillUnmount() {
    this.mounted = false;
  }

  toggleCollapsible = () => {
    this.setState(prevState => ({
      shouldOpenCollapsible: !prevState.shouldOpenCollapsible,
    }));
  }

  collapseDropdown = () => {
    this.setState({
      shouldOpenCollapsible: false
    });
  }

  render() {
    return (
      <React.Fragment>
        <div>
          <div className="catland-content">
            <MediaCollapsible className="filter-heading" dropdownTitle="Categories" id="categories" toggleCollapsible={this.toggleCollapsible} shouldOpenCollapsible={this.state.shouldOpenCollapsible}>
              <div className="catland-sibling-categories">
                <SiblingCategories
                  categoryId={this.props.categoryId}
                  collapseDropdown={this.collapseDropdown}
                  indexName={this.props.indexName}
                  parentCategoryId={this.props.category.parent_category_id}
                  setCategoryId={this.props.setCategoryId}
                />
                <div className="content-after-sidebar desktop-only" dangerouslySetInnerHTML={{ __html: this.state.cmsContent.after_sidebar }} />
              </div>
            </MediaCollapsible>
            <div className="catland-subcategories">
              <Breadcrumbs
                category={this.props.category}
                categoriesIndexName={this.props.indexName}
                setCategoryId={this.props.setCategoryId}
              />
              <Header
                category={this.props.category}
                cmsContent={this.state.cmsContent}
              />
              <Configure
                hitsPerPage={150}
              />
              <Hits
                hitComponent={({ hit }) => (
                  hit.product_count > 0 && (
                    <a
                      href={hit.url}
                      onClick={(e) => {
                        e.preventDefault();
                        this.props.setCategoryId({ hit });
                      }}
                    >
                      <img src={hit.image_url} alt="" />
                      <span className="subcategory-name">{hit.name}</span><span className="product-count">({hit.product_count})</span>
                    </a>
                  )
                )}
              />
              {this.state.cmsContent.after_content !== "" && (
                <hr className="cms-footer-border" />
              )}
              <div className="bottom-cms-content mobile-only" dangerouslySetInnerHTML={{ __html: this.state.cmsContent.after_sidebar }} />
              <div className="bottom-cms-content" dangerouslySetInnerHTML={{ __html: this.state.cmsContent.after_content }} />
            </div>
          </div>
        </div>
      </React.Fragment>
    );
  }
}

Categories.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  category: PropTypes.object.isRequired,
  categoryId: PropTypes.number.isRequired,
  indexName: PropTypes.string.isRequired,
  replaceHistory: PropTypes.func.isRequired,
  setCategoryId: PropTypes.func.isRequired,
}

export default Categories;
