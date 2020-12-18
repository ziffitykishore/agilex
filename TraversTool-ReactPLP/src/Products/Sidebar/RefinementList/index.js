import React, { PureComponent } from 'react';
import { connectRefinementList } from 'react-instantsearch-dom';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import Item from './Item';
import Swatch from './Swatch';
import ViewAll from './ViewAll';

class RefinementList extends PureComponent {
  state = {
    extended: false,
    searchTerm: '',
    viewAllDisabled: false,
    numMatches: this.props.items.length
  }

  static getDerivedStateFromProps(props, state) {
    props.setAccordionItemState(props.attribute, props.items);

    if (state.searchTerm) {
      let matches = props.items.filter(item => {
        return item.label.toLowerCase().includes(state.searchTerm);
      });
      return { numMatches: matches.length };
    }

    return { numMatches: props.items.length };
  }

  getLimit = () => {
    return this.state.extended ? this.props.showMoreLimit : this.props.limit;
  };

  onSearchFieldChange = (searchTerm) => {
    this.setState({
      searchTerm: searchTerm.toLowerCase(),
    });
  }

  onShowMoreClick = () => {
    this.setState({
      extended: !this.state.extended,
    });
  }

  needsViewAll() {
    if (this.props.showMore) {
      return this.state.numMatches > this.props.defaultLimit + 1;
    }
    return false;
  }

  render() {
    const isSwatch = this.props.displayType === 'visual-swatch';
    const isSearchable = this.props.searchable;

    const refinementListClasses = classNames({
      'refinement-list': true,
      swatches: isSwatch,
    });

    const searchableClasses = classNames({
      searchable: isSearchable
    });

    return (
      <React.Fragment>
        <div className={searchableClasses}>
          {isSearchable && (
            <div className="search-container">
              <input
                type="search"
                className="searchbox"
                onChange={event => this.onSearchFieldChange(event.currentTarget.value)}
              />
              <button type="submit" className="action search"><span></span></button>
            </div>
          )}
          <ul className={refinementListClasses}>
            {this.props.items.map((item, index) => (
              isSwatch ? (
                <Swatch
                  item={item}
                  key={item.label}
                  searchTerm={this.state.searchTerm}
                  {...this.props}
                />
              ) : (
                <Item
                  extended={this.state.extended}
                  isSearchable={isSearchable}
                  matchIndexIsGreaterThanLimit={this.state.numMatches >= index && index > this.props.defaultLimit}
                  item={item}
                  itemKey={`${item.label}-${index}-${this.props.attribute}`}
                  key={item.label}
                  numMatches={this.state.numMatches}
                  searchTerm={this.state.searchTerm}
                  createURL={this.props.createURL}
                  defaultLimit={this.props.defaultLimit}
                  isFromSearch={this.props.isFromSearch}
                  refine={this.props.refine}
                />
              )
            ))}
            {this.needsViewAll() ? (
              <ViewAll
                disabled={this.state.viewAllDisabled}
                extended={this.state.extended}
                onShowMoreClick={this.onShowMoreClick}
                showMore={this.props.showMore}
              />
            ) : null}
          </ul>
        </div>
      </React.Fragment>
    )
  }
};

RefinementList.propTypes = {
  attribute: PropTypes.string,
  createURL: PropTypes.func.isRequired,
  currentRefinement: PropTypes.array,
  defaultLimit: PropTypes.number.isRequired,
  displayType: PropTypes.string,
  isFromSearch: PropTypes.bool,
  items: PropTypes.array.isRequired,
  limit: PropTypes.number,
  refine: PropTypes.func.isRequired,
  searchable: PropTypes.bool,
  searchForItems: PropTypes.func.isRequired,
  setAccordionItemState: PropTypes.func,
  showMore: PropTypes.bool,
  showMoreLimit: PropTypes.number.isRequired,
  swatchImages: PropTypes.object,
};

export default connectRefinementList(RefinementList);
