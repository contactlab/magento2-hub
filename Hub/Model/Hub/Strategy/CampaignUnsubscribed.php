<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;
use Contactlab\Hub\Helper\Data as HubHelper;

class CampaignUnsubscribed extends HubStrategy
{
    const CHANNEL = 'EMAIL';

    protected $_helper;

    public function __construct(
        HubHelper $helper
    ){
        $this->_helper = $helper;
    }

    /**
     * Build
     *
     * @return \stdClass
     */
    public function build()
    {
        $hubEvent = new \stdClass();
        $hubEvent->properties = new \stdClass();
        $hubEvent->properties->listId = $this->_helper->getCampaignName();
        $hubEvent->properties->channel = self::CHANNEL;
        return $hubEvent;
    }
}