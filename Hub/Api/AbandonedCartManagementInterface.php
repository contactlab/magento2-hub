<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

/**
 * @api
 */
interface AbandonedCartManagementInterface
{
    /**
     * Collect Abandoned Carts
     *
     * @return AbandonedCartManagementInterface
     */
    public function collectAbandonedCarts();

}


