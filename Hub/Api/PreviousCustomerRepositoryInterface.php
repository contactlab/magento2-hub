<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Contactlab\Hub\Api\Data\PreviousCustomerInterface;

/**
 * @api
 */
interface PreviousCustomerRepositoryInterface
{
    /**
     * Save PreviousCustomer.
     *
     * @param \Contactlab\Hub\Api\Data\PreviousCustomerInterface $previousCustomer
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PreviousCustomerInterface $previousCustomer);

    /**
     * Retrieve PreviousCustomer
     *
     * @param int $previousCustomerId
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($previousCustomerId);

    /**
     * Retrieve PreviousCustomers matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete FAQ.
     *
     * @param \Contactlab\Hub\Api\Data\PreviousCustomerInterface $previousCustomer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PreviousCustomerInterface $previousCustomer);

    /**
     * Delete PreviousCustomer by ID.
     *
     * @param int $previousCustomerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($previousCustomerId);
}
