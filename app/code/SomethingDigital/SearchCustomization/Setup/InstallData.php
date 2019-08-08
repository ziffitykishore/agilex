<?php
namespace SomethingDigital\SearchCustomization\Setup;
 
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;
 
 
    /**
     * InstallData constructor.
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory
    )
    {
        $this->quoteSetupFactory = $quoteSetupFactory;
    }
 
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    { 
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable('quote'),
            'suffix',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Suffix'
            ]
        );
        $setup->endSetup();
    }
}