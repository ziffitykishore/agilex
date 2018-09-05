<?php

namespace Ziffity\Webforms\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Ziffity\Webforms\Api\Catalog\CatalogInterface;

interface CatalogRepositoryInterface
{

    public function save(CatalogInterface $data);

    public function getById($dataId);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function delete(CatalogInterface $data);

    public function deleteById($dataId);
}
