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
 * @package   mirasvit/module-search
 * @version   1.0.78
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Plugin;

use Magento\Framework\Search\Request\Config\FilesystemReader;
use Mirasvit\Search\Api\Data\Index\InstanceInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;

class RequestConfigPlugin
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    public function __construct(
        IndexRepositoryInterface $indexRepository
    ) {
        $this->indexRepository = $indexRepository;
    }

    /**
     * @param FilesystemReader $fsReader
     * @param array            $requests
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRead(
        FilesystemReader $fsReader,
        $requests
    ) {
        // add requests for all possible search indices
        foreach ($this->indexRepository->getList() as $instance) {
            $requests[$instance->getIdentifier()] = $this->generateRequest($instance);
        }

        // add requests for added indices (with fields weights)
        foreach ($this->indexRepository->getCollection() as $index) {
            $instance = $this->indexRepository->getInstance($index);
            $requests[$instance->getIdentifier()] = $this->generateRequest($instance);
        }

        return $requests;
    }

    /**
     * @param InstanceInterface $index
     * @return array
     */
    private function generateRequest($index)
    {
        $identifier = $index->getIdentifier();

        $request = [
            'dimensions'   => [
                'scope' => [
                    'name'  => 'scope',
                    'value' => 'default',
                ],
            ],
            'query'        => $identifier,
            'index'        => $identifier,
            'from'         => '0',
            'size'         => '1000',
            'filters'      => [],
            'aggregations' => [],
            'queries'      => [
                $identifier    => [
                    'type'           => 'boolQuery',
                    'name'           => $identifier,
                    'boost'          => 1,
                    'queryReference' => [
                        [
                            'clause' => 'should',
                            'ref'    => 'search_query',
                        ],
                    ],
                ],
                'search_query' => [
                    'type'  => 'matchQuery',
                    'name'  => $identifier,
                    'value' => '$search_term$',
                    'match' => [
                        [
                            'field' => '*',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($index->getAttributeWeights() as $attribute => $boost) {
            $request['queries']['search_query']['match'][] = [
                'field' => $attribute,
                'boost' => $boost,
            ];
        }

        return $request;
    }
}
