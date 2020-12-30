import "core-js";
import App from './App';
import React from 'react';
import ReactDOM from 'react-dom';
import { appDefaultProps } from './appDefaultProps'
import { BrowserRouter } from 'react-router-dom';
const { REACT_APP_ENVIRONMENT } = process.env;

const renderReactPLP = (container, props = {}) => {
  ReactDOM.render((
    <BrowserRouter>
      <App
        {...props}
      />
    </BrowserRouter>
  ), container);
}

window.renderReactPLP = renderReactPLP;

if (REACT_APP_ENVIRONMENT === 'DEV') {
  renderReactPLP(
    document.getElementById('root'), appDefaultProps
  );
}

export default renderReactPLP;
