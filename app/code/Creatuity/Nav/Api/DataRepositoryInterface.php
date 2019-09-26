<?php

namespace Creatuity\Nav\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Creatuity\Nav\Api\Data\DataInterface;

interface DataRepositoryInterface
{

    /**
     * @param DataInterface $data
     * @return mixed
     */
    public function save(DataInterface $data);

    /**
     * @param $dataId
     * @return mixed
     */
    public function getById($dataId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Creatuity\Nav\Api\Data\DataSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param DataInterface $data
     * @return mixed
     */
    public function delete(DataInterface $data);

    /**
     * @param $dataId
     * @return mixed
     */
    public function deleteById($dataId);
}
