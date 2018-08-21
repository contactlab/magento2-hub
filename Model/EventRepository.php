<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 05/05/17
 * Time: 12:37
 */

namespace Contactlab\Hub\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Contactlab\Hub\Api\Data\EventInterface;
use Contactlab\Hub\Api\Data\EventInterfaceFactory;
use Contactlab\Hub\Api\Data\EventSearchResultsInterfaceFactory;
use Contactlab\Hub\Api\EventRepositoryInterface;
use Contactlab\Hub\Model\ResourceModel\Event as EventResourceModel;
use Contactlab\Hub\Model\ResourceModel\Event\Collection;
use Contactlab\Hub\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;

class EventRepository implements EventRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Event resource model
     *
     * @var EventResourceModel
     */
    protected $resource;

    /**
     * Event collection factory
     *
     * @var EventCollectionFactory
     */
    protected $eventCollectionFactory;

    /**
     * Event interface factory
     *
     * @var EventInterfaceFactory
     */
    protected $eventInterfaceFactory;



    /**
     * @var EventSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param EventResourceModel $resource
     * @param EventCollectionFactory $eventCollectionFactory
     * @param EventInterfaceFactory $eventInterfaceFactory
     */
    public function __construct(
        EventResourceModel $resource,
        EventCollectionFactory $eventCollectionFactory,
        EventInterfaceFactory $eventInterfaceFactory,
        EventSearchResultsInterfaceFactory $eventSearchResultsInterfaceFactory
    )
    {
        $this->resource             = $resource;
        $this->eventCollectionFactory = $eventCollectionFactory;
        $this->eventInterfaceFactory  = $eventInterfaceFactory;
        $this->searchResultsFactory = $eventSearchResultsInterfaceFactory;
    }

    /**
     * Save Event.
     *
     * @param \Contactlab\Hub\Api\Data\EventInterface $event
     * @return \Contactlab\Hub\Api\Data\EventInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(EventInterface $event)
    {
        /** @var EventInterface|\Magento\Framework\Model\AbstractModel $event */
        try {
            $this->resource->save($event);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Event: %1',
                $exception->getMessage()
            ));
        }
        return $event;
    }

    /**
     * Retrieve Event.
     *
     * @param int $eventId
     * @return \Contactlab\Hub\Api\Data\EventInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($eventId)
    {
        if (!isset($this->instances[$eventId])) {
            /** @var EventInterface|\Magento\Framework\Model\AbstractModel $event */
            $event = $this->eventInterfaceFactory->create();
            $this->resource->load($event, $eventId);
            if (!$event->getId()) {
                throw new NoSuchEntityException(__('Requested Event doesn\'t exist'));
            }
            $this->instances[$eventId] = $event;
        }
        return $this->instances[$eventId];
    }

    /**
     * Retrieve Events matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\EventSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Contactlab\Hub\Api\Data\EventSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Contactlab\Hub\Model\ResourceModel\Event\Collection $collection */
        $collection = $this->eventCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'event_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var EventInterface[] $events */
        $events = [];
        /** @var \Contactlab\Hub\Model\Event $event */
        foreach ($collection as $event) {
            /** @var EventInterface $eventDataObject
            $eventDataObject = $this->eventInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $eventDataObject,
                $event->getData(),
                EventInterface::class
            );
            $events[] = $eventDataObject;
             * */
            $events[] = $event;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($events);
    }

    /**
     * Delete Event.
     *
     * @param EventInterface $event
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(EventInterface $event)
    {
        /** @var EventInterface|\Magento\Framework\Model\AbstractModel $event */
        $id = $event->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($event);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Event %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Event by ID.
     *
     * @param int $eventId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($eventId)
    {
        $event = $this->getById($eventId);
        return $this->delete($event);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    )
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
