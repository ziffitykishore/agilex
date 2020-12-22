import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Hits from './Hits';
import ViewAll from '../../ViewAll';

class List extends PureComponent {
  state = {
    productsShowingCount: 10
  }

  setProductsShowingCount = productsShowingCount => {
    this.setState({
      productsShowingCount
    });
  }

  render() {
    return (
      <React.Fragment>
        <Hits
          apiUrls={this.props.apiUrls}
          currency={this.props.currency}
          customerGroupId={this.props.customerGroupId}
          isLoadingAttributes={this.props.isLoadingAttributes}
          groupName={this.props.groupName}
          groups={this.props.groups}
          listAttributes={this.props.listAttributes}
          pricing={this.props.pricing}
          quantity={this.props.quantity}
          setGroupImage={this.props.setGroupImage}
          setProductsShowingCount={this.setProductsShowingCount}
          setQuantity={this.props.setQuantity}
          urlToGroupingImagesCatalog={this.props.urlToGroupingImagesCatalog}
        />
        <div className="view-all">
          {this.props.categoryId ? (
            <ViewAll
              min={this.props.viewMoreMinimum ? this.props.viewMoreMinimum : 10}
              productsShowingCount={this.state.productsShowingCount}
            />
          ) : (
            <ViewAll min={5000} />
          )}
        </div>
      </React.Fragment>
    )
  }
}

List.propTypes = {
  apiUrls: PropTypes.object.isRequired,
  categoryId: PropTypes.number,
  currency: PropTypes.string.isRequired,
  customerGroupId: PropTypes.number.isRequired,
  groupName: PropTypes.string,
  groups: PropTypes.array.isRequired,
  isLoadingAttributes: PropTypes.bool.isRequired,
  listAttributes: PropTypes.array.isRequired,
  pricing: PropTypes.array.isRequired,
  quantity: PropTypes.object.isRequired,
  setGroupImage: PropTypes.func,
  setQuantity: PropTypes.func.isRequired,
  urlToGroupingImagesCatalog: PropTypes.string,
  viewMoreMinimum: PropTypes.number
};

export default List;
