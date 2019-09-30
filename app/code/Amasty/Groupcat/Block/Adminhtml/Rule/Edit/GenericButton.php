<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Groupcat
 */


namespace Amasty\Groupcat\Block\Adminhtml\Rule\Edit;

use Amasty\Groupcat\Controller\RegistryConstants;
use Magento\CatalogRule\Block\Adminhtml\Edit\GenericButton as CatalogRuleGenericButton;

class GenericButton extends CatalogRuleGenericButton
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->authorization = $context->getAuthorization() ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\AuthorizationInterface::class);
        parent::__construct($context, $registry);
    }

    /**
     * Return the current Catalog Rule Id.
     *
     * @return int|null
     */
    public function getRuleId()
    {
        $groupcatRule = $this->registry->registry(RegistryConstants::CURRENT_GROUPCAT_RULE_ID);
        return $groupcatRule ? $groupcatRule->getId() : null;
    }
}
