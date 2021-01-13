import React, { PureComponent } from 'react';
import Accordion from '../Accordion';
import CurrentRefinements from './CurrentRefinements';
import RefinementList from './RefinementList';
import MediaCollapsible from '../MediaCollapsible';
import NbHitsWithUnits from '../NbHitsWithUnits';
import { ClearRefinements, Configure } from 'react-instantsearch-dom';
import Tooltip from './Tooltip';
import orderBy from 'lodash.orderby';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import Skeleton, { SkeletonTheme } from 'react-loading-skeleton';
import { debouncedForceCheck } from '../debouncedForceCheck';

class Sidebar extends PureComponent {
  state = {
    accordionItemCounts: {},
    shouldOpenCollapsible: false,
    valueSortByAttribute: {},
  }

  setAccordionItemState = (attribute, items) => {
    // Updates which accordion items should show on load and after each refinement
    if (this.state.accordionItemCounts[attribute] === items.length) {
      return;
    }

    this.setState(state => ({
      accordionItemCounts: {
        ...state.accordionItemCounts,
        [attribute]: items.length,
      }
    }));
  }

  componentDidUpdate = (prevProps) => {
    if (prevProps.searchState.refinementList != this.props.searchState.refinementList) {
      this.toggleCollapsible();

      if (window.innerWidth < 768) {
        this.setState({
          shouldOpenCollapsible: false
        });
      }
    }

    if (prevProps.isLoadingAttributes !== this.props.isLoadingAttributes) {
      if (window.innerWidth >= 768) {
        this.setState({
          shouldOpenCollapsible: true
        });
      }
    }

    debouncedForceCheck();
  }

  accordionClasses = (attribute) => {
    let groupsArray = [];

    this.props.groups.map(group => {
      groupsArray.push(group.attribute);
    });

    const classes = classNames({
      'accordion': true,
      'show-accordion': (
        groupsArray.indexOf(attribute) === -1 &&
        this.state.accordionItemCounts[attribute]
      )
    });

    return classes;
  }

  defaultRefinement = (attribute) => {
    const refinement = this.props.searchState.refinementList[attribute.id];

    if (!refinement) {
      return null;
    }

    if (Array.isArray(refinement)) {
      return refinement;
    }

    return [refinement];
  }

  toggleCollapsible = () => {
    this.setState(prevState => ({
      shouldOpenCollapsible: !prevState.shouldOpenCollapsible,
    }));
  }

  static getDerivedStateFromProps(props, state) {
    if (state.allOrderedAttributesAsDerived !== props.allOrderedAttributes) {
      let valueSortByAttribute = {};

      // Reorganize per attribute.
      props.allOrderedAttributes.forEach(orderedAttribute => {
        const attributeCode = orderedAttribute.attribute_code;
        const label = orderedAttribute.option_label;
        const sortOrder = orderedAttribute.sort_order;
        valueSortByAttribute[attributeCode] = valueSortByAttribute[attributeCode] || {};
        valueSortByAttribute[attributeCode][label] = sortOrder;
      });

      return {
        allOrderedAttributesAsDerived: props.allOrderedAttributes,
        valueSortByAttribute,
      };
    }

    return null;
  }

  sortRefinementListItems = (attribute, items) => {
    if (!this.state.valueSortByAttribute || !(attribute.id in this.state.valueSortByAttribute)) {
      return orderBy(items, 'label');
    }

    // Avoid changing the inside of items - it makes us re-render slower.
    const sortOrders = this.state.valueSortByAttribute[attribute.id];
    const sortedItems = items.sort((a, b) => {
      const sortA = sortOrders[a.label] || 999999;
      const sortB = sortOrders[b.label] || 999999;
      return sortA - sortB;
    });

    return sortedItems;
  }

  render() {
    const isDesktop = window.innerWidth > 768;
    const loadingWidths = [80, 150, 60, 0, 90, 100, 70, 60, 0, 70, 0, 150, 170, 80, 100, 0, 90, 170, 150, 50, 80]; // arbitrary

    return (
      <div className="sidebar-filters">
        <h2 className="hide-on-mobile filter-heading">Filter by</h2>
        <MediaCollapsible dropdownTitle="Filter by" className="hide-on-desktop filter-heading" id="filter-by" toggleCollapsible={this.toggleCollapsible} shouldOpenCollapsible={this.state.shouldOpenCollapsible}>
          <div className="hide-on-mobile">
            <CurrentRefinements transformItems={this.props.deduplicate} filterAttributesInfo={this.props.filterAttributesInfo} />
            <ClearRefinements
              translations={{
                reset: 'Clear Filters',
              }}
              transformItems={items =>
                items.filter(({ attribute }) => attribute !== 'categoryIds')
              }
            />
          </div>
          {this.props.isLoadingAttributes && loadingWidths.map((width, index) => (
            <SkeletonTheme color="#e4ebf0" highlightColor="#ffffff" key={index}>
              <Skeleton duration={1.7} count={1} height={25} width={width} />
            </SkeletonTheme>
          ))}
          {!this.props.isLoadingAttributes && this.props.filterAttributesInfo.map(attribute => {
            if(Object.keys(this.state.valueSortByAttribute[attribute.id]).length === 1) return null;
            return (
            <div className={this.accordionClasses(attribute.id)} key={attribute.id}>
              <Accordion classNames="filter-content" dropdownTitle={attribute.label || attribute.id} isDesktop={isDesktop}>
                {attribute.description && (
                  <Tooltip attribute={attribute} />
                )}
                <RefinementList
                  attribute={attribute.id}
                  defaultRefinement={this.defaultRefinement(attribute)}
                  defaultLimit={5}
                  displayType={attribute.displayType}
                  limit={10000}
                  searchable={attribute.searchable !== '0'}
                  setAccordionItemState={this.setAccordionItemState}
                  showMore={true}
                  showMoreLimit={this.props.maxVisibleAttributes ? this.props.maxVisibleAttributes : 15}
                  swatchImages={this.props.swatchImages}
                  transformItems={items => this.sortRefinementListItems(attribute, items)}
                />
              </Accordion>
            </div>
          )})}
          <Configure hitsPerPage={8} />
        </MediaCollapsible>
        <div className="hide-on-desktop">
          <CurrentRefinements transformItems={this.props.deduplicate} filterAttributesInfo={this.props.filterAttributesInfo} />
          <ClearRefinements
            translations={{
              reset: 'Clear Filters',
            }}
            transformItems={items =>
              items.filter(({ attribute }) => attribute !== 'categoryIds')
            }
          />
          <div className="sidebar-num-hits">
            <NbHitsWithUnits />
          </div>
        </div>
      </div>
    );
  }
}

Sidebar.propTypes = {
  allOrderedAttributes: PropTypes.array.isRequired,
  deduplicate: PropTypes.func,
  filterAttributesInfo: PropTypes.array.isRequired,
  groups: PropTypes.array,
  isLoadingAttributes: PropTypes.bool.isRequired,
  maxVisibleAttributes: PropTypes.number,
  searchState: PropTypes.object,
  swatchImages: PropTypes.object,
};

export default Sidebar;
