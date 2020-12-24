import React, { PureComponent } from 'react';
import Collapsible from 'react-collapsible';
import Media from 'react-media';
import PropTypes from 'prop-types';
import classNames from 'classnames';

class MediaCollapsible extends PureComponent {
  render() {
    const collapsibleClasses = classNames({
      [this.props.className]: true,
      'collapsible-open': this.props.shouldOpenCollapsible === true,
      'collapsible-closed': this.props.shouldOpenCollapsible === false,
    });

    const shouldShowCollapsibleClass = classNames({
      'show-collapsible': this.props.shouldOpenCollapsible === true,
      'hide-collapsible': this.props.shouldOpenCollapsible === false,
    });

    return (
      <Media query={this.props.query}>
        {matches => matches ? (
          <React.Fragment>
            <button
              type="button"
              onClick={this.props.toggleCollapsible}
              className={collapsibleClasses}
              aria-expanded={this.props.shouldOpenCollapsible}
              aria-controls={this.props.id}
            >
              {this.props.dropdownTitle}
            </button>

            <React.Fragment>
              <div className={shouldShowCollapsibleClass} id={this.props.id}>
                {this.props.children}
              </div>
            </React.Fragment>
          </React.Fragment>
        ) : (
          <React.Fragment>{this.props.children}</React.Fragment>
        )}
      </Media>
    );
  }
}

MediaCollapsible.defaultProps = {
  query: '(max-width: 768px)',
}

MediaCollapsible.propTypes = {
  query: PropTypes.string,
  dropdownTitle: PropTypes.string.isRequired,
  children: PropTypes.node.isRequired,
  className: PropTypes.string,
  id: PropTypes.string.isRequired,
  shouldOpenCollapsible: PropTypes.bool.isRequired,
  toggleCollapsible: PropTypes.func.isRequired,
};

export default MediaCollapsible;
