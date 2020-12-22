import React, { PureComponent } from 'react';
import Collapsible from 'react-collapsible';
import PropTypes from 'prop-types';

class Accordion extends PureComponent {
  render() {
    return (
      <Collapsible openedClassName="Collapsible__opened" trigger={this.props.dropdownTitle} open={this.props.isDesktop}>
        {this.props.children}
      </Collapsible>
    )
  }
}

Accordion.propTypes = {
  children: PropTypes.node.isRequired,
  dropdownTitle: PropTypes.string.isRequired,
  isDesktop: PropTypes.bool
};

export default Accordion;
