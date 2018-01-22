<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 14/12/17
 * Time: 16:16
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;

class FormCompiled extends HubStrategy
{
    /**
     * Build
     *
     * @return \stdClass
     */
    public function build()
    {
        $hubEvent = new \stdClass();
        $hubEvent->properties = new \stdClass();
        $hubEvent->properties->formName = 'registeredEcommerce';
        $hubEvent->properties->formId = 'registeredEcommerce';
        return $hubEvent;
    }
}