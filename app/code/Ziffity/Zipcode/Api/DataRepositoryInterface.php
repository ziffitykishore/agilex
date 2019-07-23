<?php

namespace Ziffity\Zipcode\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Ziffity\Zipcode\Api\Data\DataInterface;

interface DataRepositoryInterface
{

    public function save(DataInterface $data);

    public function getById($dataId);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function delete(DataInterface $data);

    public function deleteById($dataId);
}
