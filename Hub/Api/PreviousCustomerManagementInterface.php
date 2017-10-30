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
interface PreviousCustomerManagementInterface
{
    /**
     * Collect Previous Customer
     *
     * @return PreviousCustomerManagementInterface
     */
    public function collectPreviousCustomers();

    /**
     * Reset Previous Customer
     *
     * @return PreviousCustomerManagementInterface
     */
    public function resetPreviousCustomers();
}


