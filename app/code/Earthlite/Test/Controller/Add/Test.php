<?php

namespace Earthlite\Test\Controller\Add;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Test extends Action
{
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }


    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $commaSeperatedSkuList = '6502,6508,6520,6521,6501,6510,6511,6507,6517,6504,6518,6513,6505,6713,6013,6113,6002,6005,6008,6020,6021,6001,6010,6011,6007,6017,6004,6018,6102,6105,6108,6120,6121,6101,6110,6111,6107,6117,6104,6118,9302,9305,9308,9320,9321,9301,9310,9311,9307,9317,9304,9318,9313,9413,9402,9405,9408,9420,9421,9401,9410,9411,9407,9417,9404,9418,9513,9502,9505,9508,9520,9521,9501,9510,9511,9507,9517,9504,9518,9608,9610,9611,9708,9710,9711,9808,9810,9811,6702,6705,6708,6720,6721,6701,6710,6711,6707,6717,6704,6718,9113,9102,9105,9108,9120,9121,9101,9110,9111,9107,9117,9104,9118,9213,9202,9205,9208,9220,9221,9201,9210,9211,9207,9217,9204,9218,7513,7713,7502,7505,7508,7520,7521,7501,7510,7511,7507,7517,7504,7518,7702,7705,7708,7720,7721,7701,7710,7711,7707,7717,7704,7718,3704,3721,3707,3702';
        $skuList = explode(",", $commaSeperatedSkuList);

        echo "Total Number of SKU's = ". count($skuList);
        echo "</br>Product Update Starts";

        foreach($skuList as $sku)
        {
            $productId = $objectManager->create('Magento\Catalog\Model\Product')->getIdBySku($sku);

            if($productId) {
                try {
                    $productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory');
                    $product = $productFactory->create()->load($productId);
                    $newSku = "0".$sku;
                    $product->setSku($newSku);
                    $product->save();
                    echo "</br>";
                    echo "SKU Updated = ".$sku;
                }
                catch (\Exception $e) {
                    echo "Cannot retrieve products from Magento: ".$e->getMessage()."<br>"; 
                }
            }else {
                echo "</br>";
                echo "Invalid SKU = ".$sku;
            }
        }
    }
}
