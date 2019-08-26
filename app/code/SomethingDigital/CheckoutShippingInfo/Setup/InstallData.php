<?php

namespace SomethingDigital\CheckoutShippingInfo\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\DB\Ddl\Table;


class InstallData implements InstallDataInterface
{
    /**
     * SalesSetupFactory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * QuoteSetupFactory
     *
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * InstallData constructor.
     *
     * @param SalesSetupFactory $salesSetupFactory SalesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory QuoteSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * Install data
     *
     * @param ModuleDataSetupInterface $setup   ModuleDataSetupInterface
     * @param ModuleContextInterface   $context ModuleContextInterface
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->setup = $setup->startSetup();
        $this->installQuoteData();
        $this->installSalesData();
        $this->setup = $setup->endSetup();
    }

    /**
     * Install quote custom data
     *
     * @return void
     */
    public function installQuoteData()
    {
        $quoteInstaller = $this->quoteSetupFactory->create(
            [
                'resourceName' => 'quote_setup',
                'setup' => $this->setup
            ]
        );

        $quoteInstaller
            ->addAttribute(
                'quote',
                'checkout_ponumber',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true]
            )
            ->addAttribute(
                'quote',
                'checkout_shiptopo',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true]
            )
            ->addAttribute(
                'quote',
                'checkout_deliverypoint',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true]
            )
            ->addAttribute(
                'quote',
                'checkout_ordernotes',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true]
            );
    }

    /**
     * Install sales custom data
     *
     * @return void
     */
    public function installSalesData()
    {
        $salesInstaller = $this->salesSetupFactory->create(
            [
                'resourceName' => 'sales_setup',
                'setup' => $this->setup
            ]
        );
        $salesInstaller
            ->addAttribute(
                'order',
                'checkout_ponumber',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'grid' => false]
            )
            ->addAttribute(
                'order',
                'checkout_shiptopo',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'grid' => false]
            )
            ->addAttribute(
                'order',
                'checkout_deliverypoint',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'grid' => false]
            )
            ->addAttribute(
                'order',
                'checkout_ordernotes',
                ['type' => Table::TYPE_TEXT, 'length' => '255', 'nullable' => true, 'grid' => false]
            );
    }
}
