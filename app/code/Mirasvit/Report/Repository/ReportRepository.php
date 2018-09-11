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
 * @package   mirasvit/module-report
 * @version   1.3.35
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Repository;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Mirasvit\Report\Api\Data\ReportInterface;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * @var ReportInterface[]
     */
    private $pool = [];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string[]
     */
    private $reports;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $reports = []
    ) {
        $this->objectManager = $objectManager;
        $this->reports = $reports;
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier, $id = null)
    {
        foreach ($this->getList() as $report) {
            if ($report->getIdentifier() == strtolower($identifier)) {
                if (!$report->getId()) {
                    $report->setId($id);
                }

                $report->init();

                return $report;
            }
        }

        throw new \Exception(__('Report %1 is not defined.', $identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $this->initPool();

        return $this->pool;
    }

    /**
     * @return $this
     */
    private function initPool()
    {
        if (count($this->pool)) {
            return $this;
        }

        foreach ($this->reports as $report) {
            $this->pool[] = $this->objectManager->get($report);
        }

        return $this;
    }
}