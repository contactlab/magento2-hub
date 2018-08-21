<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 05/05/17
 * Time: 14:18
 */
namespace Contactlab\Hub\Api\Data;

/**
 * @api
 */
interface PreviousCustomerSearchResultsInterface
{
    /**
     * Get Events list.
     *
     * @return \Contactlab\Hub\Api\Data\PreviousCustomerInterface[]
     */
    public function getItems();

    /**
     * Set Events list.
     *
     * @param \Contactlab\Hub\Api\Data\PreviousCustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
