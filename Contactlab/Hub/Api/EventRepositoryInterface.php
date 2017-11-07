<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Contactlab\Hub\Api\Data\EventInterface;

/**
 * @api
 */
interface EventRepositoryInterface
{
    /**
     * Save Event.
     *
     * @param \Contactlab\Hub\Api\Data\EventInterface $event
     * @return \Contactlab\Hub\Api\Data\EventInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(EventInterface $event);

    /**
     * Retrieve Event
     *
     * @param int $eventId
     * @return \Contactlab\Hub\Api\Data\EventInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($eventId);

    /**
     * Retrieve Events matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\EventSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete FAQ.
     *
     * @param \Contactlab\Hub\Api\Data\EventInterface $event
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(EventInterface $event);

    /**
     * Delete Event by ID.
     *
     * @param int $eventId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($eventId);
}
