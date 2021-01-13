import AddToCart from '../../AddToCart';
import QuantityIncrementMessage from '../../QuantityIncrementMessage';
import QuantityInput from '../../QuantityInput';
import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connectHits } from 'react-instantsearch-dom';
import { getGroupImage, renderHitProperty, round } from '../../helpers';
import classNames from 'classnames';
import LazyLoad from 'react-lazyload';

// You will see both "formated" and "formatted" in this code. Algolia made a typo and we had to go with it

class Hits extends PureComponent {
  componentDidMount = () => {
    if (this.props.setGroupImage) {
      const { hits, urlToGroupingImagesCatalog } = this.props;
      const groupImage = getGroupImage({ hits, urlToGroupingImagesCatalog });

      this.props.setGroupImage({
        [this.props.groupName]: groupImage
      })
    }
  }

  render() {
    this.props.setProductsShowingCount(this.props.hits.length);

    return (
      <div className="list-view">
        {this.props.hits.filter(hit => hit.in_stock).map(hit => {
          const sku = renderHitProperty(hit.sku);
          const spotPrice = this.props.pricing.find(product => product.sku === renderHitProperty(hit.sku)) || {};
          const rawTierGroups = hit[`group_${this.props.customerGroupId}_tiers`] || hit.group_32000_tiers;
          const lowestTierPrice = rawTierGroups && JSON.parse(rawTierGroups).map(tier => tier.price).reduce((a, b) => Math.min(a, b));
          const currencySymbol = this.props.currency === 'CAD' ? 'CA $' : '$';
          
          const strikethrough = classNames({
            strikethrough: spotPrice.price && spotPrice.price != 0,
          });

          const listAttributes = this.props.listAttributes ? this.props.listAttributes.filter(attr => (
            (this.props.isLoadingAttributes || hit[attr.id]) &&
            attr.id && attr.id !== 'sku' && attr.id !== 'price'
          )).slice(0, 4) : [];

          const listItem = (
            <div className="list-item" key={sku}>
              <div className="list-inner">
                <div className="list-image">
                  <a href={hit.url}>
                    <img src={hit.image_url} alt={hit.name} />
                  </a>
                </div>
                <div className="list-content">
                  <div className="list-content-left">
                    <a dangerouslySetInnerHTML={{ __html: hit.name }} href={hit.url} className="product-name"></a>
                    <div className="item-and-attributes">
                      <p className="product-sku">Item # {sku}</p>
                      
                      <ul className="list-content-right">
                        {listAttributes.map(listAttribute => (
                          <li key={listAttribute.id}>
                            <span className="attribute-label">{`${listAttribute.label && listAttribute.label}`}</span>:
                            <span>{` ${renderHitProperty(hit[listAttribute.id])}`}</span>
                          </li>
                        ))}
                      </ul>
                    </div>
                    <div className="pricing">
                      {hit.manufacturer_exact_unit_price ? (
                        hit.manufacturer_exact_unit_price > hit.price[this.props.currency].default && (
                          <p className="msrp">MSRP: {currencySymbol + round(hit.manufacturer_exact_unit_price)}</p>
                        )
                      ) : (
                        hit.manufacturer_price.price > hit.price[this.props.currency].default && (
                          <p className="msrp">MSRP: {hit.manufacturer_price.price_formated}</p>
                        )
                      )}
                      {rawTierGroups ? (
                        <p className="as-low-as">As low as {currencySymbol + Number(lowestTierPrice).toFixed(2)}</p>
                      ) : (
                        <React.Fragment>
                          {hit.exact_unit_price ? (
                            <p className="price"><span>{currencySymbol + round(hit.exact_unit_price)}</span></p>
                          ) : (
                            <React.Fragment>
                              {hit.price[this.props.currency].default_original_formated && (
                                !spotPrice.price && (
                                  <span className="price"><span className="strikethrough">{hit.price[this.props.currency].default_original_formated}</span></span>
                                )
                              )}
                              <span className="price"><span className={strikethrough}>{hit.price[this.props.currency].default_formated}</span></span>
                              {spotPrice.price && spotPrice.price != 0 && <p className="spot-price price">{spotPrice.price_formatted}</p>}
                            </React.Fragment>
                          )}
                        </React.Fragment>
                      )}
                    </div>
                    {hit.unit_of_measure && <div style={{fontSize:'12px'}}><strong>UOM :</strong> {hit.unit_of_measure}</div> }             
                  </div>
                </div>
              </div>
              <div className="cart-actions">
                <QuantityIncrementMessage
                  hit={hit}
                />
                <div className="quantity-and-cart">
                  <QuantityInput
                    hit={hit}
                    quantity={this.props.quantity[sku]}
                    setQuantity={this.props.setQuantity.bind(this, sku)}
                  />
                  <AddToCart
                    apiUrls={this.props.apiUrls}
                    hit={hit}
                    quantity={this.props.quantity[sku]}
                  />
                </div>
              </div>
            </div>
          );

          if (this.props.groups) {
            return listItem;
          }

          return (
            <LazyLoad key={`${sku}-lazy`} height={200} offset={3500} once>
              {listItem}
            </LazyLoad>
          )
        })}
      </div>
    );
  }
}

Hits.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  groupName: PropTypes.string,
  groups: PropTypes.array.isRequired,
  hits: PropTypes.array.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  listAttributes: PropTypes.array.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  setGroupImage: PropTypes.func,
  setProductsShowingCount: PropTypes.func.isRequired,
  setQuantity: PropTypes.func.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string
};

export default connectHits(Hits);
