<?php

namespace SomethingDigital\BuildNotation\Plugin;

use Magento\Framework\App\ProductMetadataInterface;

class PayPalConfigPlugin
{
    const BN_OPEN_SOURCE = 'SD_SI_MagentoCE';
    const BN_COMMERCE = 'SD_SI_MagentoEE';

    private $productMetadata;

    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    public function afterGetBuildNotationCode($subject, $result)
    {
        $edition = $this->productMetadata->getEdition();
        if ($edition === 'Community' || $edition === 'Open Source') {
            return static::BN_OPEN_SOURCE;
        }
        return static::BN_COMMERCE;
    }
}
