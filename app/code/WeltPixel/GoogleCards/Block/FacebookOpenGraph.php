<?php

namespace WeltPixel\GoogleCards\Block;

class FacebookOpenGraph extends GoogleCards
{

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product)
    {
        if ($this->_helper->getFacebookDescriptionType() == 1) {
            return nl2br($product->getData('description'));
        } elseif ($this->_helper->getFacebookDescriptionType() == 2) {
            return nl2br($product->getData('meta_description'));
        } else {
            return nl2br($product->getData('short_description'));
        }
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->_helper->getFacebookSiteName();
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->_helper->getFacebookAppId();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getFacebookOpenGraphPrice();
        return $this->_calculatePrice($priceOption);
    }
}
