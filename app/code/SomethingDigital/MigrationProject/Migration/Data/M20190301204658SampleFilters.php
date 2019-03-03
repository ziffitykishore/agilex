<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\TableFactory;
use Magento\Framework\App\State;

class M20190301204658SampleFilters implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    protected $attributeRepository;
    protected $attributeValues;
    protected $tableFactory;

    private $productRepository;

    public function __construct(
        PageHelper $page, 
        BlockHelper $block, 
        EmailHelper $email, 
        ResourceConfig $resourceConfig, 
        EavSetup $eavSetup, 
        EavSetupFactory $eavSetupFactory, 
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $attributeRepository,
        TableFactory $tableFactory,
        State $appState
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->eavSetup = $eavSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $appState->setAreaCode('adminhtml');
    }

    public function execute(SetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
         
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sample_attribute',
            [
                'type' => 'int',
                'label' => 'Sample Atrribute',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'option' => ['values' => ['Value 1', 'Value 2', 'Value 3']],
            ]
        );

        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sample_attribute', 'searchable_in_layered_nav', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sample_attribute', 'layered_nav_description', 'This is sample description');
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sample_attribute', 'include_in_table', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sample_attribute', 'include_in_flyout', 1);

        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'searchable', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'filterable', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'searchable_in_layered_nav', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'layered_nav_description', 'This is sample description');
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'include_in_table', 1);
        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'color', 'include_in_flyout', 1);
        
        // Set product attribute value
        $product = $this->productRepository->getById(1);
        $product->setSampleAttribute($this->getOptionId('sample_attribute', 'Value 1'));
        $this->productRepository->save($product);
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @return int|false
     */
    public function getOptionId($attributeCode, $label)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if (!isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }

    /**
     * Get attribute by code.
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }
}
