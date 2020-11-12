<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cenpos\SimpleWebpay\Block;

use \Magento\Framework\View\Element\Template\Context;
use \Magento\Payment\Helper\Data as PaymentHelper;
use Cenpos\SimpleWebpay\Gateway\Http\Client\ClientMock;
use Cenpos\SimpleWebpay\Model\Ui\ConfigProvider;

class Form extends \Magento\Payment\Block\Form
{
	/** @var \Magento\Payment\Model\MethodInterface */
	private $_method;

	/**
	 * Form constructor.
	 * @param Context $context
	 * @param PaymentHelper $paymentHelper
	 * @param array $data
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function __construct(
		Context $context,
		PaymentHelper $paymentHelper,
		array $data = []
	) {
		$this->_method = $paymentHelper->getMethodInstance(ConfigProvider::CODE);

		parent::__construct($context, $data);
	}

	/**
	 * @return array
	 */
	public function getTransactionResults()
	{
		return [
			ClientMock::SUCCESS => __('Success'),
			ClientMock::FAILURE => __('Fraud')
		];
	}

	/**
	 * @return mixed
	 */
	public function getSwpUrl()
	{
		return $this->_method->getConfigData('url');
	}

	/**
	 * @return mixed
	 */
	public function getIsCvv()
	{
		return $this->_method->getConfigData('iscvv');
	}

	/**
	 * @return string
	 */
	public function getIsToken19()
	{
		return ($this->_method->getConfigData('usetoken') === "token19") ? "true" : "false";
	}

	/**
	 * @return string
	 */
	public function getUseToken()
	{
		return (($this->_method->getConfigData('usetoken') === "1")? "false" : "true");
	}

	/**
	 * @return string
	 */
	public function getAdminUrlSave()
	{
		return $this->_urlBuilder->getUrl('simplewebpay/index/index');
	}

	/**
	 * @return string
	 */
	public function getAdminSession()
	{
		return $this->_urlBuilder->getUrl('simplewebpay/index/sessiondata');
	}

	/**
	 * @return string
	 */
	public function getLoaderImage()
	{
		return $this->getImage("Cenpos_SimpleWebpay::images/loader.gif");
	}


	/**
	 * @param $name
	 * @return string
	 */
	private function getImage($name){
		$params = array('_secure' => $this->_request->isSecure());
		return $this->_assetRepo->getUrlWithParams($name, $params);
	}
}
