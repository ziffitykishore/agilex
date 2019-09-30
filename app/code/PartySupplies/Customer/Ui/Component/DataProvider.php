<?php

namespace PartySupplies\Customer\Ui\Component;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use PartySupplies\Customer\Helper\Constant;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param AttributeRepository   $attributeRepository
     * @param array                 $meta
     * @param array                 $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $criteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AttributeRepository $attributeRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $criteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     *
     * @return void
     */
    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        $params = $this->request->getParams();
        if (isset($params['nav_customer_id']) && $params['account_type'] === 'company') {

            $this->setNavCutomerIdFilter($params['nav_customer_id'], Constant::CUSTOMER);

        } elseif (isset($params['nav_customer_id']) && $params['account_type'] === 'customer') {

            $this->setNavCutomerIdFilter($params['nav_customer_id'], Constant::COMPANY);

        } else {
            
            foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
                if ('*' == $paramValue) {
                    $paramValue = $this->request->getParam($paramName);
                }
                if ($paramValue) {
                    $this->data['config']['update_url'] = sprintf(
                        '%s%s/%s/',
                        $this->data['config']['update_url'],
                        $paramName,
                        $paramValue
                    );
                    $this->addFilter(
                        $this->filterBuilder->setField($paramName)
                            ->setValue($paramValue)
                            ->setConditionType('eq')
                            ->create()
                    );
                }
            }
        }
    }

    /**
     *
     * @param string $navId
     * @param string $accountType
     */
    protected function setNavCutomerIdFilter($navId, $accountType)
    {
        $this->addFilter(
            $this->filterBuilder->setField('nav_customer_id')->setValue($navId)->setConditionType('eq')->create()
        );
        $this->addFilter(
            $this->filterBuilder->setField('account_type')->setValue($accountType)->setConditionType('eq')->create()
        );
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($this->attributeRepository->getList() as $attributeCode => $attributeData) {
            foreach ($data['items'] as &$item) {
                if (isset($item[$attributeCode]) && !empty($attributeData[AttributeMetadataInterface::OPTIONS])) {
                    $item[$attributeCode] = explode(',', $item[$attributeCode]);
                }
            }
        }
        return $data;
    }
}
