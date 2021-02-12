import React, { PureComponent } from 'react';
import { renderHitProperty } from './helpers';
import enhanceWithClickOutside from 'react-click-outside';
import PropTypes from 'prop-types';

class QuantityInput extends PureComponent {
  state = {
    value: this.props.quantity || this.props.hit.min_sale_qty
  }

  componentDidUpdate = (prevProps, prevState) => {
    const quantityChanged = prevProps.quantity !== this.props.quantity;
    const hitChanged = prevProps.hit !== this.props.hit;
    const currentValueChanged = prevState.value !== this.state.value;

    if (quantityChanged || hitChanged) {
      this.setState({
        value: this.props.quantity || this.props.hit.min_sale_qty
      });
    }

    if (currentValueChanged) {
      if (this.state.value == this.props.quantity || this.props.hit.min_sale_qty) {
        this.props.setQuantity(this.state.value);
      }
    }
  }

  setNewValue = value => {
    this.setState({
      value
    });
  }

  handleClickOutside() {
    // if (this.state.value) {
    //   return;
    // }
    if(this.props.hit.qty_increment){
        const incrementQty = this.props.hit.qty_increment;
        let updateQty = parseInt(Math.ceil( this.state.value / incrementQty) * incrementQty);
        updateQty = ( updateQty == 0) ? this.state.value : updateQty;
        this.props.setQuantity(updateQty);
    }
    //this.setNewValue(this.props.hit.min_sale_qty ? this.props.hit.min_sale_qty : 1);
  }

  render() {
    const quantityInputId = `quantity-${renderHitProperty(this.props.hit.sku)}`;

    return (
      <div className="quantity-input">
        <label htmlFor={this.props.hit.sku}>Qty</label>
        <input
          id={quantityInputId}
          type="number"
          onClick={() => this.setNewValue('')}
          onFocus={() => this.setNewValue('')}
          onBlur={this.handleClickOutside.bind(this)}
          value={this.state.value}
          step={this.props.hit.qty_increment ? this.props.hit.qty_increment : 1}
          onChange={e => this.props.setQuantity(e.target.value)}
        />
      </div>
    )
  }
}

QuantityInput.propTypes = {
  hit: PropTypes.object.isRequired,
  quantity: PropTypes.node.isRequired,
  setQuantity: PropTypes.func.isRequired,
}

export default enhanceWithClickOutside(QuantityInput);
