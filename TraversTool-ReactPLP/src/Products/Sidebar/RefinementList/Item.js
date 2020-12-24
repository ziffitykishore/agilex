import React, { Component } from 'react';
import { Highlight } from 'react-instantsearch-dom';
import equals from 'is-equal-shallow';
import PropTypes from 'prop-types';

const itemEqual = (a, b) => {
  for (let k in a) {
    if (k !== 'value' && a[k] !== b[k]) {
      return false;
    }
  }

  return equals(a.value, b.value);
};

class Item extends Component {
  state = {
    isSelected: false
  }

  shouldComponentUpdate(nextProps, nextState) {
    if (!equals(nextState, this.state)) {
      return true;
    }
    if (equals(nextProps, this.props)) {
      return false;
    }
    for (let k in nextProps) {
      if (k !== 'item' && nextProps[k] !== this.props[k]) {
        return true;
      }
    }

    return !itemEqual(nextProps.item, this.props.item);
  }

  componentDidMount = () => {
    if (this.props.item.isRefined) {
      this.setState({
        isSelected: true
      });
    }
  }

  componentDidUpdate = (prevProps, prevState) => {
    if (prevProps.item.isRefined !== this.props.item.isRefined) {
      this.setState({
        isSelected: this.props.item.isRefined
      });
    }
  }

  selectRefinement = () => {
    const { refine, item } = this.props;

    this.setState(prevState => ({
      isSelected: !prevState.isSelected,
    }));

    setTimeout(() => refine(item.value), 100); // prevents content flickering
  }

  render() {
    const { extended, matchIndexIsGreaterThanLimit, item, itemKey } = this.props;
    const noMatch = item.label.toLowerCase().indexOf(this.props.searchTerm) === -1;
    const searchReturnedNoMatches = this.props.isSearchable && noMatch;

    if (searchReturnedNoMatches || (!extended && matchIndexIsGreaterThanLimit)) {
      return null;
    }

    return (
      <li key={itemKey}>
        <React.Fragment>
          <input
            id={itemKey}
            type="checkbox"
            className="refinement-checkbox"
            checked={this.state.isSelected}
            onClick={() => {
              this.selectRefinement();
            }}
          />
          <label className="refinement-label" htmlFor={itemKey}>
            <a
              href={this.props.createURL(item.value)}
              rel="nofollow"
              onClick={event => {
                event.preventDefault();
                this.selectRefinement();
              }}
            >
              {this.props.isFromSearch ? (
                <Highlight attribute="label" hit={item} />
              ) : (
                item.label
              )}{' '}
              ({item.count})
          </a>
          </label>
        </React.Fragment>
      </li>
    );
  }
}

Item.propTypes = {
  extended: PropTypes.bool,
  createURL: PropTypes.func.isRequired,
  isFromSearch: PropTypes.bool.isRequired,
  isSearchable: PropTypes.bool.isRequired,
  matchIndexIsGreaterThanLimit: PropTypes.bool.isRequired,
  item: PropTypes.object.isRequired,
  itemKey: PropTypes.string.isRequired,
  refine: PropTypes.func.isRequired,
  searchTerm: PropTypes.string,
};

export default Item;
