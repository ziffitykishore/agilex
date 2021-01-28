import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';


export const ProductsContext = React.createContext({});

const ProductsContextProvider = (props) => {
    const [products, setProducts] = useState({});
    const [totalCount, setTotalCount] = useState({});
    const [position, setPosition] = useState({});

    useEffect(() => {
        if (Object.keys(totalCount).length) {
            var newO = {};
            const newTotalCount = {...totalCount}
            Object.keys(newTotalCount).sort(function (a, b) { return newTotalCount[b] - newTotalCount[a] })
                .map((key, index) => newO[key] = index);
            setPosition(newO);
        }
    }, [totalCount]);
    useEffect(() => {
        let productsKeys = Object.keys(products);
        if (productsKeys.length) {
            let newTotalCount = {  };
            productsKeys.map(key => {
                let totalOrders = 0;
                products[key].map(product => {
                    if (product.ig_torders_12month) {
                        totalOrders += product.sku_torders_12month;
                    }
                })
                newTotalCount[key] = totalOrders;
            })
            setTotalCount(newTotalCount);

        }
    }, [products]);
    return <ProductsContext.Provider value={{ products, setProducts, setTotalCount, totalCount,position }}>
        {props.children}
    </ProductsContext.Provider>
}
ProductsContextProvider.propTypes = {
    children: PropTypes.object.isRequired,
};
export default ProductsContextProvider;