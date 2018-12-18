<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Directory\Model\ResourceModel\Currency;

class M20181218182923SetupCurrencySettings implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;

    /**
     * @var Currency
     */
    private $currency;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email, ResourceConfig $resourceConfig, Currency $currency)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->currency = $currency;
    }

    public function execute(SetupInterface $setup)
    {
        $this->resourceConfig->saveConfig('currency/options/allow', 'USD', 'default', 0);
        $this->resourceConfig->saveConfig('currency/options/default', 'CAD', 'stores', 2);
        $this->resourceConfig->saveConfig('currency/options/allow', 'CAD', 'stores', 2);

        $rates = [
            'USD' => [
               'CAD' => 1.3500, 
            ],
            'CAD' => [
               'USD' => 0.7407, 
            ],
        ];
        $this->currency->saveRates($rates);
    }
}
