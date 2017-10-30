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
interface AbandonedCartSearchResultsInterface
{
    /**
     * Get Events list.
     *
     * @return \Contactlab\Hub\Api\Data\AbandonedCartInterface[]
     */
    public function getItems();

    /**
     * Set Events list.
     *
     * @param \Contactlab\Hub\Api\Data\AbandonedCartInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
