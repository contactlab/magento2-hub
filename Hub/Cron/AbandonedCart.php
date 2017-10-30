<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 22/06/17
 * Time: 14:51
 */

namespace Contactlab\Hub\Cron;

use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Api\AbandonedCartManagementInterface;

class AbandonedCart
{
    protected $_helper;
    protected $_abandonedCartService;

    public function __construct(
        HubHelper $helper,
        AbandonedCartManagementInterface $abandonedCartService
    ) {
        $this->_helper = $helper;
        $this->_abandonedCartService = $abandonedCartService;
    }

    public function execute()
    {
        $this->_helper->log(__METHOD__);
        $this->_abandonedCartService->collectAbandonedCarts();
        return $this;
    }
}