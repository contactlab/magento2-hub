<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 30/06/17
 * Time: 16:39
 */

namespace Contactlab\Hub\Cron;

use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Api\PreviousCustomerManagementInterface;

class PreviousCustomer
{
    protected $_helper;
    protected $_previousCustomerService;

    public function __construct(
        HubHelper $helper,
        PreviousCustomerManagementInterface $previousCustomerService
    )
    {
        $this->_helper = $helper;
        $this->_previousCustomerService = $previousCustomerService;
    }

    public function execute()
    {
        $this->_helper->log(__METHOD__);
        $pageSize = $this->_helper->getPreviousCustomerPageSize();
        $this->_previousCustomerService->collectPreviousCustomers($pageSize);
        return $this;
    }
}