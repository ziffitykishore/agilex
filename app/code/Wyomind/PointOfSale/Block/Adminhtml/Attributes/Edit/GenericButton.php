<?php

namespace Wyomind\PointOfSale\Block\Adminhtml\Attributes\Edit;

/**
 * Class GenericButton
 * @package Wyomind\PointOfSale\Block\Adminhtml\Attributes\Edit
 */
class GenericButton
{
    /**
     * Url Builder
     * @var \Magento\Framework\UrlInterface 
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }
    
    /**
     * Return the current rule ID
     * 
     * @return int|null
     */
    public function getId()
    {
        $attribute = $this->registry->registry('attribute');
        return $attribute ? $attribute->getId() : null;
    }
    
    /**
     * Generate url by route and parameters
     * 
     * @param type $route
     * @param type $params
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
    
    /**
     * Check where button can be rendered
     *
     * @param string $name
     * @return string
     */
    public function canRender($name)
    {
        return $name;
    }
}