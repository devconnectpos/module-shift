<?php
namespace SM\Shift\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RetailTransactionRepositoryInterface
{
    public function save(\SM\Shift\Api\Data\RetailTransactionInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(\SM\Shift\Api\Data\RetailTransactionInterface $page);

    public function deleteById($id);
}
