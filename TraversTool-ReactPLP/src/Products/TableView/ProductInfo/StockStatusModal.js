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
    const donotshow = this.props.hit.wh_sc_status === 0 && this.props.hit.wh_ny_status === 0 && this.props.hit.wh_ca_status === 0;
    return (
      <div>
        <Modal
          onRequestClose={this.props.closeStockModal}
          isOpen={this.props.isShowingStockModal}
        >
          <button className="modal-close-btn" type="button" aria-label="close" onClick={this.props.closeStockModal}>×</button>
          <h2>Check warehouse stock availability</h2>
          {donotshow ?
          <div style={{textAlign:"center"}}>
             Stock Information not available for this item.
          </div> :
          <table>
            <thead>
              <tr>
              {tableHeaders.map(header => (
                <th key={header}><strong>{`${header}:`}</strong></th>
              ))}
              </tr>
            </thead>
            <tbody>
              {this.props.hit.wh_sc_status !== 0 &&
              <tr> 
                <td><span>{this.props.hit.sku}</span></td>
                <td><span>Duncan, SC</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_sc_qty)}</span></td>
                <td><span>{this.props.hit.wh_sc_qty}</span></td>
              </tr>
              }
              {this.props.hit.wh_ny_qty !== 0 &&
              <tr>
                <td><span>{this.props.hit.sku}</span></td>
                <td><span>Queens, NY</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_ny_qty)}</span></td>
                <td><span>{this.props.hit.wh_ny_qty}</span></td>
              </tr>
              }
              {this.props.hit.wh_ca_qty !== 0 &&
              <tr>
               <td><span>{this.props.hit.sku}</span></td>
                <td><span>Chatsworth, CA</span></td>
                <td><span>{this.determineStockStatus(this.props.hit.wh_ca_qty)}</span></td>
                <td><span>{this.props.hit.wh_ca_qty}</span></td>
              </tr>
              }
            </tbody>
          </table>
  }
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
