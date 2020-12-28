import React from 'react';
import PropTypes from 'prop-types';

export default function ViewSwitcher(props) {
  const tableIcon = <svg className="table-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M988.9 98.7c-2.6-31.5-28.1-58.1-60.1-58.1H71.2c-32 0-57.5 26.5-60.2 58.1h-1v799.4c0 33.8 27.4 61.2 61.2 61.2h857.5c33.8 0 61.2-27.4 61.2-61.2V98.7h-1zM316.2 898.1h-245V714.4h245v183.7zm0-239.9h-245V469.4h245v188.8zm0-250.1h-245V224.4h245v183.7zm306.3 490h-245V714.4h245v183.7zm0-239.9h-245V469.4h245v188.8zm0-250.1h-245V224.4h245v183.7zm306.3 490h-245V714.4h245v183.7zm0-239.9h-245V469.4h245v188.8zm0-250.1h-245V224.4h245v183.7z" /></svg>
  const listIcon = <svg className="list-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M377.5 71.3H990v122.5H377.5V71.3zm0 367.5H990v122.5H377.5V438.8zm0 367.5H990v122.5H377.5V806.3zM10 132.5C10 64.8 64.8 10 132.5 10S255 64.8 255 132.5 200.2 255 132.5 255 10 200.2 10 132.5zM10 500c0-67.7 54.8-122.5 122.5-122.5S255 432.3 255 500s-54.8 122.5-122.5 122.5S10 567.7 10 500zm0 367.5C10 799.8 64.8 745 132.5 745S255 799.8 255 867.5 200.2 990 132.5 990 10 935.2 10 867.5z" /></svg>
  return (
    <div className="product-view-toggles">
      <button type="button" onClick={() => props.setView('table')} className={props.active === 'table' ? 'active' : ''}>{tableIcon} Table</button>
      <button type="button" onClick={() => props.setView('list')} className={props.active === 'list' ? 'active' : ''}>{listIcon} List</button>
    </div>
  );
}

ViewSwitcher.propTypes = {
  active: PropTypes.string.isRequired,
  setView: PropTypes.func.isRequired,
};
