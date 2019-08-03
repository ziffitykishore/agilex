<?php
namespace Wyomind\PointOfSale\Ui\Component\Listing\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{

    const editUrl = "pointofsale/attributes/edit";
    const deleteUrl = "pointofsale/attributes/delete";


    /**
     * @var \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder 
     */
    protected $_urlBuilder;



    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                if (isset($item['attribute_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl(self::editUrl, ['attribute_id' => $item['attribute_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(self::deleteUrl, ['attribute_id' => $item['attribute_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete an attribute'),
                            'message' => __('Are you sure you want to delete the attribute <b>%1</b> [<i>%2</i>]?', $item['label'], $item['code'])
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}