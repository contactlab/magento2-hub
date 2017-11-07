<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 14:53
 */

namespace Contactlab\Hub\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Contactlab\Hub\Api\Data\PreviousCustomerInterface;
use Contactlab\Hub\Api\Data\PreviousCustomerInterfaceFactory;
use Contactlab\Hub\Api\Data\PreviousCustomerSearchResultsInterfaceFactory;
use Contactlab\Hub\Api\PreviousCustomerRepositoryInterface;
use Contactlab\Hub\Model\ResourceModel\PreviousCustomer as PreviousCustomerResourceModel;
use Contactlab\Hub\Model\ResourceModel\PreviousCustomer\Collection;
use Contactlab\Hub\Model\ResourceModel\PreviousCustomer\CollectionFactory as PreviousCustomerCollectionFactory;

class PreviousCustomerRepository implements PreviousCustomerRepositoryInterface
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
     * @var PreviousCustomerResourceModel
     */
    protected $resource;

    /**
     * PreviousCustomer collection factory
     *
     * @var PreviousCustomerCollectionFactory
     */
    protected $previousCustomerCollectionFactory;

    /**
     * PreviousCustomer interface factory
     *
     * @var PreviousCustomerInterfaceFactory
     */
    protected $previousCustomerInterfaceFactory;



    /**
     * @var PreviousCustomerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param PreviousCustomerResourceModel $resource
     * @param PreviousCustomerCollectionFactory $previousCustomerCollectionFactory
     * @param PreviousCustomerInterfaceFactory $previousCustomerInterfaceFactory
     */
    public function __construct(
        PreviousCustomerResourceModel $resource,
        PreviousCustomerCollectionFactory $previousCustomerCollectionFactory,
        PreviousCustomerInterfaceFactory $previousCustomerInterfaceFactory,
        PreviousCustomerSearchResultsInterfaceFactory $previousCustomerSearchResultsInterfaceFactory
    )
    {
        $this->resource             = $resource;
        $this->previousCustomerCollectionFactory = $previousCustomerCollectionFactory;
        $this->previousCustomerInterfaceFactory  = $previousCustomerInterfaceFactory;
        $this->searchResultsFactory = $previousCustomerSearchResultsInterfaceFactory;
    }

    /**
     * Save PreviousCustomer.
     *
     * @param \Contactlab\Hub\Api\Data\PreviousCustomerInterface $previousCustomer
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PreviousCustomerInterface $previousCustomer)
    {
        /** @var PreviousCustomerInterface|\Magento\Framework\Model\AbstractModel $previousCustomer */
        try {
            $this->resource->save($previousCustomer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the PreviousCustomer: %1',
                $exception->getMessage()
            ));
        }
        return $previousCustomer;
    }

    /**
     * Retrieve PreviousCustomer.
     *
     * @param int $previousCustomerId
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($previousCustomerId)
    {
        if (!isset($this->instances[$previousCustomerId])) {
            /** @var PreviousCustomerInterface|\Magento\Framework\Model\AbstractModel $previousCustomer */
            $previousCustomer = $this->previousCustomerInterfaceFactory->create();
            $this->resource->load($previousCustomer, $previousCustomerId);
            if (!$previousCustomer->getId()) {
                throw new NoSuchEntityException(__('Requested PreviousCustomer doesn\'t exist'));
            }
            $this->instances[$previousCustomerId] = $previousCustomer;
        }
        return $this->instances[$previousCustomerId];
    }

    /**
     * Retrieve PreviousCustomers matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Contactlab\Hub\Api\Data\PreviousCustomerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Contactlab\Hub\Model\ResourceModel\PreviousCustomer\Collection $collection */
        $collection = $this->previousCustomerCollectionFactory->create();

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
            $field = 'previous_customer_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var PreviousCustomerInterface[] $previousCustomers */
        $previousCustomers = [];
        /** @var \Contactlab\Hub\Model\PreviousCustomer $previousCustomer */
        foreach ($collection as $previousCustomer) {
            /** @var PreviousCustomerInterface $previousCustomerDataObject
            $previousCustomerDataObject = $this->previousCustomerInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
            $previousCustomerDataObject,
            $previousCustomer->getData(),
            PreviousCustomerInterface::class
            );
            $previousCustomers[] = $previousCustomerDataObject;
             * */
            $previousCustomers[] = $previousCustomer;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($previousCustomers);
    }

    /**
     * Delete PreviousCustomer.
     *
     * @param PreviousCustomerInterface $previousCustomer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PreviousCustomerInterface $previousCustomer)
    {
        /** @var PreviousCustomerInterface|\Magento\Framework\Model\AbstractModel $previousCustomer */
        $id = $previousCustomer->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($previousCustomer);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove PreviousCustomer %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete PreviousCustomer by ID.
     *
     * @param int $previousCustomerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($previousCustomerId)
    {
        $previousCustomer = $this->getById($previousCustomerId);
        return $this->delete($previousCustomer);
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