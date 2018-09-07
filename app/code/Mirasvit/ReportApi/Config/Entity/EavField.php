<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Config\Entity;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Mirasvit\ReportApi\Api\Config\SelectInterface;

class EavField extends Field
{
    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var int
     */
    protected $entityTypeId;

    /**
     * @var string
     */
    protected $eavTableAlias;

    /**
     * @var AttributeInterface
     */
    protected $attribute;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        ProductMetadataInterface $productMetadata,
        $table,
        $name,
        $entityTypeId
    ) {
        parent::__construct($table, $name);

        $this->productMetadata = $productMetadata;

        $this->eavTableAlias = $this->table->getName() . '_' . $this->name;
        $this->attributeRepository = $attributeRepository;
        $this->entityTypeId = $entityTypeId;

        $this->attribute = $this->attributeRepository->get($this->entityTypeId, $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function toDbExpr()
    {
        if ($this->attribute->getBackend()->isStatic()) {
            return $this->table->getName() . '.' . $this->name;
        } else {
            return $this->eavTableAlias . '.value';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function join(SelectInterface $select)
    {
        if ($this->attribute->getBackend()->isStatic()) {
            return $select->joinTable($this->table);
        } else {
            $conditions = [];
            if ($this->productMetadata->getEdition() == 'Enterprise') {
                $conditions[] = $this->eavTableAlias . '.row_id = ' . $this->table->getName() . '.row_id';
            } else {
                $conditions[] = $this->eavTableAlias . '.entity_id = ' . $this->table->getName() . '.entity_id';
            }
            $conditions[] = $this->eavTableAlias . '.attribute_id = ' . $this->attribute->getAttributeId();
            $conditions[] = $this->eavTableAlias . '.store_id = 0';

            $select->joinTable($this->table);

            return $select->leftJoin(
                [$this->eavTableAlias => $this->attribute->getBackend()->getTable()],
                implode(' AND ', $conditions),
                []
            );
        }
    }
}
