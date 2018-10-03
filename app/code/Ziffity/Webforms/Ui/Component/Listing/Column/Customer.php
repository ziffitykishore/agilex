<?php
namespace Ziffity\Webforms\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Customer extends Column
{
    protected $escaper;

    protected $systemStore;

    protected $customerFactory;
    
    protected $urlBuilder;
    
    const CUSTOMER_URL_PATH_EDIT = 'customer/index/edit';
    
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlInterface $urlBuilder,            
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $html = 'Guest';
                if($item['customer_id']){
                    $customer = $this->customerFactory->create()->load($item['customer_id']);
                    $html = html_entity_decode('<a href="'.$this->urlBuilder->getUrl(self::CUSTOMER_URL_PATH_EDIT, ['id' => $item['customer_id']]).'">'.$customer->getName().'</a>');
                }
                $item['customer_id'] = $html;
            }
        }
        return $dataSource;
    }
}
