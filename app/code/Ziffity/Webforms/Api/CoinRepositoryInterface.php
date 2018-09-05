<?php

namespace Ziffity\Webforms\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Ziffity\Webforms\Api\Coin\CoinInterface;

interface CoinRepositoryInterface
{

    public function save(CoinInterface $data);

    public function getById($dataId);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function delete(CoinInterface $data);

    public function deleteById($dataId);
}
