import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import Modal from 'react-modal';

class StockStatusModal extends PureComponent {
  determineStockStatus = quantity => {
    if (quantity === 0) {
      return 'Out of stock';
    } else {
      return 'In stock';
    }
  }

  render() {
    const tableHeaders = ['SKU', 'Warehouse', 'Status', 'Qty'];

    return (
      <div>
        <Modal
          onRequestClose={this.props.closeStockModal}
          isOpen={this.props.isShowingStockModal}
        >
          <button className="modal-close-btn" type="button" aria-label="close" onClick={this.props.closeStockModal}>×</button>
          <h2>Check warehouse stock availability</h2>
          <table>
            <thead>
              <tr>
              {tableHeaders.map(header => (
                <th key={header}><strong>{`${header}:`}</strong></th>
              ))}
              </tr>
            </thead>
            <tbody>
              <tr> 
                <td><span>{this.props.hit.sku}</span></td>
                <td><span>Duncan, SC</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_sc_qty)}</span></td>
                <td><span>{this.props.hit.wh_sc_qty}</span></td>
              </tr>
              <tr>
                <td><span>{this.props.hit.sku}</span></td>
                <td><span>Queens, NY</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_ny_qty)}</span></td>
                <td><span>{this.props.hit.wh_ny_qty}</span></td>
              </tr>
              <tr>
               <td><span>{this.props.hit.sku}</span></td>
                <td><span>Chatsworth, CA</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_ca_qty)}</span></td>
                <td><span>{this.props.hit.wh_ca_qty}</span></td>
              </tr>
            </tbody>
          </table>
        </Modal>
      </div>
    )
  }
}

StockStatusModal.propTypes = {
  closeStockModal: PropTypes.func.isRequired,
  hit: PropTypes.object.isRequired,
  isShowingStockModal: PropTypes.bool.isRequired,
};

export default StockStatusModal;
