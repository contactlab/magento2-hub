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
interface EventStrategyInterface
{
    /**
     * Set Context
     *
     * @param $context
     * @return $this
     */
    public function setContext($context);

    /**
     * Build
     *
     * @return array
     */
    public function build();
}