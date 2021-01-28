import React, { PureComponent } from 'react';
import AddToCart from '../../AddToCart';
import QuantityIncrementMessage from '../../QuantityIncrementMessage';
import QuantityInput from '../../QuantityInput';
import { renderHitProperty } from '../../helpers';
import classNames from 'classnames';
import PropTypes from 'prop-types';
import { round, formatPrice } from '../../helpers';
import StockStatusModal from './StockStatusModal';

// You will see both "formated" and "formatted" in this code. Algolia made a typo and we had to go with it

class ProductInfo extends PureComponent {
  state = {
    clearMessage: false,
    isShowingStockModal: false
  }

  openStockModal = () => {
    this.setState({
      isShowingStockModal: true
    });
  }

  closeStockModal = () => {
    this.setState({
      isShowingStockModal: false
    });
  }

  closeFlyout = () => {
    this.props.selectProduct({});
    this.props.checkProductInfoView(false);
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevProps.hit && prevProps.hit != this.props.hit) {
      this.setState({
        clearMessage: !prevState.clearMessage,
      });
    }
  }

  render() {
    if (!Object.keys(this.props.hit).length) {
      return null;
    }

    const spotPrice = this.props.pricing.find(product => product.sku === renderHitProperty(this.props.hit.sku)) || {};
    const rawTierGroups = this.props.hit[`group_${this.props.customerGroupId}_tiers`] || this.props.hit.group_32000_tiers;
    const lowestTierPrice = rawTierGroups && JSON.parse(rawTierGroups).map(tier => tier.price).reduce((a, b) => Math.min(a, b));
    const currencySymbol = this.props.currency === 'CAD' ? 'CA $' : '$';
    const defaultQuantity = this.props.quantity[this.props.hit.sku] || 1;
    let parsedTierGroups;

    if (rawTierGroups) {
      parsedTierGroups = JSON.parse(rawTierGroups)
    }

    const strikethrough = classNames({
      strikethrough: rawTierGroups || spotPrice.price && spotPrice.price < this.props.hit.defaultPrice.default,
    });

    return (
      <div className="product-info">
        <div className="product-info-content">
          <div className="product-info-content--inner">
            <button aria-label="Close" className="close-button" type="button" onClick={this.closeFlyout}>×</button>
            <a href={this.props.hit.url}><img src={this.props.hit.image_url} alt={this.props.hit.name}></img></a>
            <a href={this.props.hit.url} className="product-name"><p dangerouslySetInnerHTML={{ __html: this.props.hit.name }}></p></a>
            {this.props.hit.sx_inventory_status === "Order as needed" ?
            <span>Ships from Mfr.</span>
              :
              <button type="button" className="stock-modal-trigger" onClick={this.openStockModal}>Check Locations</button>
            }
            <StockStatusModal
              closeStockModal={this.closeStockModal}
              hit={this.props.hit}
              isShowingStockModal={this.state.isShowingStockModal}
            />
            <p className="product-sku">Item # {this.props.hit.sku}</p>
            <div className="pricing">
              {this.props.hit.manufacturer_exact_unit_price ? (
                this.props.hit.manufacturer_exact_unit_price > this.props.hit.defaultPrice.default && (
                  <p className="msrp">MSRP per 100: {currencySymbol + formatPrice(round(this.props.hit.manufacturer_exact_unit_price * 100))}</p>
                )
              ) : (
                this.props.hit.msrp.price > this.props.hit.defaultPrice.default && (
                  <p className="msrp">MSRP: {this.props.hit.msrp.price_formated}</p>
                )
              )}
              {rawTierGroups ? (
                <p className="as-low-as">As low as {currencySymbol + Number(lowestTierPrice).toFixed(2)}</p>
              ) : (
                <React.Fragment>
                  {this.props.hit.exact_unit_price ? (
                    <React.Fragment>
                      <p className="price"><span>Price per 100: {currencySymbol + formatPrice(round(this.props.hit.exact_unit_price * 100))}</span></p>
                      <p className="price price-per-unit"><span>{currencySymbol + round(this.props.hit.exact_unit_price)} each</span></p>
                    </React.Fragment>
                  ) : (
                    <React.Fragment>
                      {this.props.hit.special_exact_unit_price ? (
                        <p>{currencySymbol + round(this.props.hit.special_exact_unit_price)}</p>
                      ) : (
                        <p className="price">
                          {this.props.hit.defaultPrice.default_original_formated && (
                            <span className="price"><span className="strikethrough">{this.props.hit.defaultPrice.default_original_formated}</span></span>
                          )}
                          <span className={strikethrough}>{this.props.hit.defaultPrice.default_formated}</span>
                          <span className="spot-price">{spotPrice.price != 0 && spotPrice.price_formatted}</span>
                        </p>
                      )}
                    </React.Fragment>
                  )}
                </React.Fragment>
              )}
            </div>
            <ul>
              {this.props.flyoutAttributes.slice(0, 4).map(flyoutAttribute => (
                renderHitProperty(this.props.hit[flyoutAttribute.id]) != undefined && (
                  <li key={flyoutAttribute.id}>
                    {flyoutAttribute.label && flyoutAttribute.label + ':'} {renderHitProperty(this.props.hit[flyoutAttribute.id])}
                  </li>
                )
              ))}
            </ul>
            {parsedTierGroups && (
              <table className="tier-group-table">
                <tbody>
                  <tr className="tier-labels">
                    <th>Qty</th>
                    <th>Price (ea.)</th>
                  </tr>
                  {parsedTierGroups.map((tier, index) => (
                    <tr className="tier-values" key={tier.qty}>
                      <td key={tier.qty}>
                        <strong>
                          {`${tier.qty}`}
                          {index === parsedTierGroups.length - 1 && '+'}
                        </strong>
                      </td>
                      <td key={tier.qty}>{tier.price_formatted}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
            <QuantityIncrementMessage hit={this.props.hit} />
            <QuantityInput
              hit={this.props.hit}
              quantity={defaultQuantity}
              setQuantity={this.props.setQuantity.bind(this, this.props.hit.sku)}
            />
            <a className="view-details" href={this.props.hit.url}>View Details</a>
            <AddToCart
              apiUrls={this.props.apiUrls}
              hit={this.props.hit}
              quantity={defaultQuantity}
              clearMessage={this.state.clearMessage}
            />
          </div>
        </div>
      </div>
    );
  }
}

ProductInfo.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  checkProductInfoView: PropTypes.func.isRequired,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  flyoutAttributes: PropTypes.array.isRequired,
  hit: PropTypes.object.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  selectProduct: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired
};

export default ProductInfo;
