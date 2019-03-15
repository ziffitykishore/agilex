<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

/**
 * Feed edit form General Configuration tab block
 */
namespace RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Tab;

/**
 * Feed edit form General Configuration tab
 */
class General extends \RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Tab\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Directory\AvailableCurrencies
     */
    protected $currencies;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes
     */
    protected $sourceAttributes;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Converter $feedConverter
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Source\Directory\AvailableCurrencies $currencies
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesno
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes $sourceAttributes
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\Converter $feedConverter,
        \Magento\Store\Model\System\Store $systemStore,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Directory\AvailableCurrencies $currencies,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Attributes $sourceAttributes,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->currencies = $currencies;
        $this->sourceYesno = $sourceYesno;
        $this->sourceAttributes = $sourceAttributes;
        parent::__construct($context, $registry, $formFactory, $feedConverter, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /* @var $model \RocketWeb\ShoppingFeeds\Model\Feed */
        $model = $this->_coreRegistry->registry('feed');

        if ($this->_isAllowedAction('RocketWeb_ShoppingFeeds::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('feed_');

        $fieldset = $form->addFieldset('feed_settings', ['legend' => __('Feed Settings')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset->addField('type', 'hidden', ['name' => 'type', 'value' => $model->getType()]);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'note' => __('The name of the Feed'),
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, false),
                    'disabled' => $isElementDisabled,
                    'note' => __('Specify from which store the feed will pull data.'),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'config_general_currency',
            'select',
            [
                'name' => 'config[general_currency]',
                'label' => __('Feed Currency'),
                'title' => __('Feed Currency'),
                'required' => true,
                'values' => $this->currencies->toOptionArray(),
                'disabled' => $isElementDisabled,
                'note' => __('This lists only allowed currencies on the store view.<br />WARNING: Changing to a currency which is not displayed on frontend can lead to feed being rejected with provider!<br />Don\'t forget to update rates when adding new currency to the store.'),
            ]
        );

        $fieldset->addField(
            'config_general_feed_dir',
            'text',
            [
                'name' => 'config[general_feed_dir]',
                'label' => __('Feed path'),
                'title' => __('Feed path'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'note' => __('It\'s the dir path to save the feed. Assure write permissions.'),
            ]
        );

        $fieldset = $form->addFieldset('general_configuration', ['legend' => __('General Configuration')]);

        $fieldset->addField(
            'config_general_apply_catalog_price_rules',
            'select',
            [
                'name' => 'config[general_apply_catalog_price_rules]',
                'label' => __('Apply Catalog Price Rules'),
                'title' => __('Apply Catalog Price Rules'),
                'required' => true,
                'values' => $this->sourceYesno->toOptionArray(),
                'disabled' => $isElementDisabled,
                'note' => __('To exclude catalog promo price set this to No.'),
            ]
        );

        $fieldset->addField(
            'config_general_use_default_stock',
            'select',
            [
                'name' => 'config[general_use_default_stock]',
                'label' => __('Use default Stock Statuses'),
                'title' => __('Use default Stock Statuses'),
                'required' => true,
                'values' => $this->sourceYesno->toOptionArray(),
                'disabled' => $isElementDisabled,
                'note' => __('If your store is using a custom attribute for stock status, change this to No.'),
            ]
        );

        $fieldset->addField(
            'config_general_stock_attribute_code',
            'select',
            [
                'name' => 'config[general_stock_attribute_code]',
                'label' => __('Alternate Stock/Availability Attribute'),
                'title' => __('Alternate Stock/Availability Attribute'),
                'required' => false,
                'values' => $this->sourceAttributes->toOptionArray(true),
                'disabled' => $isElementDisabled,
                'note' => __('To fill \'availability\'. The attribute\'s values can be: \'in stock\', \'available for order\', \'out of stock\', \'preorder\'. Other values will be replaced by \'out of stock\'.'),
            ]
        );
        
        $fieldset->addField(
            'config_general_use_qty_increments',
            'select',
            [
                'name' => 'config[general_use_qty_increments]',
                'label' => __('Use Qty Increments'),
                'title' => __('Use Qty Increments'),
                'required' => true,
                'values' => $this->sourceYesno->toOptionArray(),
                'disabled' => $isElementDisabled,
                'note' => __('To use or not product qty increments'),
            ]
        );

        $this->_eventManager->dispatch(sprintf('adminhtml_feed_edit_tab_general_prepare_form_%s', $model->getType()), [
            'form' => $form,
            'feed' => $model,
            'is_element_disabled' => $isElementDisabled,
        ]);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "feed_config_general_stock_attribute_code",
                'config[general_stock_attribute_code]'
            )->addFieldMap(
                "feed_config_general_use_default_stock",
                'config[general_use_default_stock]'
            )->addFieldDependence(
                'config[general_stock_attribute_code]',
                'config[general_use_default_stock]',
                0
            )
        );

        $form->setValues($this->prepareValues($model));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Configuration');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Configuration');
    }
}
