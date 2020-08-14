<?php
/**
 * Created by pp
 *
 * @project magento216
 */

namespace Unirgy\RapidFlowSales\Helper\ProtectedCode;

use Magento\Customer\Model\CustomerFactory;
use Unirgy\RapidFlow\Model\Config as ModelConfig;
use Unirgy\RapidFlow\Model\Io\CsvFactory;
use Unirgy\RapidFlowSales\Helper\Data as RapidFlowSalesHelperData;
use Unirgy\RapidFlow\Helper\Data as RfHelper;

class Context
{
    public $helperData;
    public $customerFactory;
    public $rfConfig;
    public $ioCsvFactory;
    /**
     * @var RfHelper
     */
    public $rfHelper;

    public function __construct(
        RfHelper $rfHelper,
        RapidFlowSalesHelperData $helperData,
        CustomerFactory $modelCustomerFactory,
        ModelConfig $modelConfig,
        CsvFactory $ioCsvFactory
    )
    {
        $this->helperData      = $helperData;
        $this->customerFactory = $modelCustomerFactory;
        $this->rfConfig        = $modelConfig;
        $this->ioCsvFactory    = $ioCsvFactory;
        $this->rfHelper = $rfHelper;
    }
}
