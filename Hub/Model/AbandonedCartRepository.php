<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 11:20
 */

namespace Contactlab\Hub\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Contactlab\Hub\Api\Data\AbandonedCartInterface;
use Contactlab\Hub\Api\Data\AbandonedCartInterfaceFactory;
use Contactlab\Hub\Api\Data\AbandonedCartSearchResultsInterfaceFactory;
use Contactlab\Hub\Api\AbandonedCartRepositoryInterface;
use Contactlab\Hub\Model\ResourceModel\AbandonedCart as AbandonedCartResourceModel;
use Contactlab\Hub\Model\ResourceModel\AbandonedCart\Collection;
use Contactlab\Hub\Model\ResourceModel\AbandonedCart\CollectionFactory as AbandonedCartCollectionFactory;

class AbandonedCartRepository implements AbandonedCartRepositoryInterface
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
     * @var AbandonedCartResourceModel
     */
    protected $resource;

    /**
     * AbandonedCart collection factory
     *
     * @var AbandonedCartCollectionFactory
     */
    protected $abandonedCartCollectionFactory;

    /**
     * AbandonedCart interface factory
     *
     * @var AbandonedCartInterfaceFactory
     */
    protected $abandonedCartInterfaceFactory;



    /**
     * @var AbandonedCartSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param AbandonedCartResourceModel $resource
     * @param AbandonedCartCollectionFactory $abandonedCartCollectionFactory
     * @param AbandonedCartInterfaceFactory $abandonedCartInterfaceFactory
     */
    public function __construct(
        AbandonedCartResourceModel $resource,
        AbandonedCartCollectionFactory $abandonedCartCollectionFactory,
        AbandonedCartInterfaceFactory $abandonedCartInterfaceFactory,
        AbandonedCartSearchResultsInterfaceFactory $abandonedCartSearchResultsInterfaceFactory
    )
    {
        $this->resource             = $resource;
        $this->abandonedCartCollectionFactory = $abandonedCartCollectionFactory;
        $this->abandonedCartInterfaceFactory  = $abandonedCartInterfaceFactory;
        $this->searchResultsFactory = $abandonedCartSearchResultsInterfaceFactory;
    }

    /**
     * Save AbandonedCart.
     *
     * @param \Contactlab\Hub\Api\Data\AbandonedCartInterface $abandonedCart
     * @return \Contactlab\Hub\Api\Data\AbandonedCartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(AbandonedCartInterface $abandonedCart)
    {
        /** @var AbandonedCartInterface|\Magento\Framework\Model\AbstractModel $abandonedCart */
        try {
            $this->resource->save($abandonedCart);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the AbandonedCart: %1',
                $exception->getMessage()
            ));
        }
        return $abandonedCart;
    }

    /**
     * Retrieve AbandonedCart.
     *
     * @param int $abandonedCartId
     * @return \Contactlab\Hub\Api\Data\AbandonedCartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($abandonedCartId)
    {
        if (!isset($this->instances[$abandonedCartId])) {
            /** @var AbandonedCartInterface|\Magento\Framework\Model\AbstractModel $abandonedCart */
            $abandonedCart = $this->abandonedCartInterfaceFactory->create();
            $this->resource->load($abandonedCart, $abandonedCartId);
            if (!$abandonedCart->getId()) {
                throw new NoSuchEntityException(__('Requested AbandonedCart doesn\'t exist'));
            }
            $this->instances[$abandonedCartId] = $abandonedCart;
        }
        return $this->instances[$abandonedCartId];
    }

    /**
     * Retrieve AbandonedCarts matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Contactlab\Hub\Api\Data\AbandonedCartSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Contactlab\Hub\Api\Data\AbandonedCartSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Contactlab\Hub\Model\ResourceModel\AbandonedCart\Collection $collection */
        $collection = $this->abandonedCartCollectionFactory->create();

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
            $field = 'abandoned_cart_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var AbandonedCartInterface[] $abandonedCarts */
        $abandonedCarts = [];
        /** @var \Contactlab\Hub\Model\AbandonedCart $abandonedCart */
        foreach ($collection as $abandonedCart) {
            /** @var AbandonedCartInterface $abandonedCartDataObject
            $abandonedCartDataObject = $this->abandonedCartInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
            $abandonedCartDataObject,
            $abandonedCart->getData(),
            AbandonedCartInterface::class
            );
            $abandonedCarts[] = $abandonedCartDataObject;
             * */
            $abandonedCarts[] = $abandonedCart;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($abandonedCarts);
    }

    /**
     * Delete AbandonedCart.
     *
     * @param AbandonedCartInterface $abandonedCart
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(AbandonedCartInterface $abandonedCart)
    {
        /** @var AbandonedCartInterface|\Magento\Framework\Model\AbstractModel $abandonedCart */
        $id = $abandonedCart->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($abandonedCart);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove AbandonedCart %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete AbandonedCart by ID.
     *
     * @param int $abandonedCartId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($abandonedCartId)
    {
        $abandonedCart = $this->getById($abandonedCartId);
        return $this->delete($abandonedCart);
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