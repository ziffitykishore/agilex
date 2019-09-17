<?php

namespace Creatuity\MagNav\Model\Connector;


use Magento\Framework\ObjectManagerInterface;

class SoapConnectorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SoapConnectorFactory constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return SoapConnector
     */
    public function create($navintModule, array $args = [])
    {
        return $this->objectManager->create(
            SoapConnector::class,
            [
                'magnavModule' => $navintModule,
            ] + $args
        );
    }

}