import React, { PureComponent } from 'react'
import PropTypes from 'prop-types';
import { renderHitProperty } from './helpers';
import _ from 'underscore';
import classNames from 'classnames';

class AddToCart extends PureComponent {
  state = {
    messageObject: {
      messages: []
    },
    addingToCart: false,
  }

  componentDidUpdate(prevProps) {
    if (prevProps.clearMessage != this.props.clearMessage) {
      this.setState({
        messageObject: {
          messages: []
        },
        addingToCart: false,
      })
    }
  }

  addProductToCart = (e) => {
    this.setState({
      addingToCart: true,
    });

    e.preventDefault();
    const formKey = window.cookieStorage.getItem('form_key');
    const addToCartBaseUrl = this.props.apiUrls.addToCart.url;
    const addToCartUrl = `${addToCartBaseUrl}/${renderHitProperty(this.props.hit.objectID)}/form_key/${formKey}/qty/${this.props.quantity}`;

    // Hook into the Magento customer-data.js to track messages when adding items to the cart
    window.require(['Magento_Customer/js/customer-data'], customerData => {
      const subscription = customerData.get('messages').subscribe(message => {
        this.setState({
          messageObject: message
        });
        subscription.dispose();
      });

      const prefix = process.env.API_CORS_PREFIX || '';
      return fetch(prefix + addToCartUrl, {
        method: 'POST',
        headers: {
          'Content-type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest',
        }
      }).then(() => {
        const cookieStorageMessages = _.unique(window.cookieStorage.getItem('mage-messages'));
        this.setState({
          messageObject: {
            ...this.state.messageObject,
            messages: [
              ...this.state.messageObject.messages,
              cookieStorageMessages
            ],
          },
        });
        customerData.reload(['cart', 'messages'], true);
      }).then(() => {
        setTimeout(() => {
          this.setState({
            addingToCart: false,
          })
        }, 800); // this is only to adjust the animation speed
      });
    });
  }

  render() {
    const spinner = <div className="cart-spinner"><div></div><div></div><div></div><div></div></div>;

    return (
      <React.Fragment>
        {this.props.hit.type_id.toLowerCase() === 'simple' ? (
          <button type="button" className="add-to-cart action primary" onClick={this.addProductToCart}>
            {this.state.addingToCart ? spinner : 'Add To Cart'}
          </button>
        ) : (
          <a className="add-to-cart action primary" href={this.props.hit.url}>Add To Cart</a>
        )}
        {this.state.messageObject.messages != [] && (
          this.state.messageObject.messages.map(message => {
            const messageClasses = classNames({
              message: true,
              [message.type]: true,
              'hide': this.state.addingToCart == true,
            });
            return (
              <p key={message.text} className={messageClasses} dangerouslySetInnerHTML={{ __html: message.text }} />
            )
          })
        )}
      </React.Fragment>
    );
  }
}

AddToCart.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  clearMessage: PropTypes.bool,
  hit: PropTypes.object.isRequired,
  quantity: PropTypes.node.isRequired,
};

export default AddToCart;
