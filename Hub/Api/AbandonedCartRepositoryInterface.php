<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Contactlab\Hub\Api\Data\AbandonedCartInterface;

/**
 * @api
 */
interface AbandonedCartRepositoryInterface
{
    /**
     * Save AbandonedCart.
     *
     * @param \Contactlab\Hub\Api\Data\AbandonedCartInterface $abandonedCart
     * @return \Contactlab\Hub\Api\Data\AbandonedCartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(AbandonedCartInterface $abandonedCart);

    /**
     * Retrieve AbandonedCart
     *
     * @param int $abandonedCartId
     * @return \Contactlab\Hub\Api\Data\AbandonedCartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($abandonedCartId);

    /**
     * Retrieve AbandonedCarts matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\AbandonedCartSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete FAQ.
     *
     * @param \Contactlab\Hub\Api\Data\AbandonedCartInterface $abandonedCart
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(AbandonedCartInterface $abandonedCart);

    /**
     * Delete AbandonedCart by ID.
     *
     * @param int $abandonedCartId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($abandonedCartId);
}
