<?php 

namespace Ziffity\Bundle\Block\Catalog\Product\View\Type;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle as CatalogBundle;
use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;


class Bundle extends CatalogBundle
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Bundle\Model\Product\PriceFactory $productPrice,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $catalogProduct,
            $productPrice,
            $jsonEncoder,
            $localeFormat,
            $data
        );
    }  
    
    public function getOptionHtml(Option $option)
    {
        $optionBlock = $this->getChildBlock($option->getType());

        if (!$optionBlock) {
            return __('There is no defined renderer for "%1" option type.', $option->getType());
        }
        
        if(!$option->getRequired()) {
            return false;
        }
        return $optionBlock->setOption($option)->toHtml();
    }

    public function getOptionAddonsHtml(Option $option)
    {

        $optionBlock = $this->getChildBlock($option->getType());
        if (!$optionBlock) {
            return __('There is no defined renderer for "%1" option type.', $option->getType());
        }

        if($option->getRequired()) {
            return false;
        }
        return $optionBlock->setOption($option)->toHtml();
    }        
}
