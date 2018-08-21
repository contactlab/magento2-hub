<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 27/11/17
 * Time: 16:35
 */

namespace Contactlab\Hub\Plugin\Customer\Model;

use Magento\Customer\Model\EmailNotification;
use Magento\Customer\Api\Data\CustomerInterface;
use Contactlab\Hub\Helper\Data as HubHelper;

class EmailNotificationPlugin
{

    protected $_helper;

    public function __construct(
        HubHelper $helper
    )
    {
        $this->_helper = $helper;
    }



    public function aroundNewAccount(
        EmailNotification $subject,
        \Closure $proceed,
        CustomerInterface $customer,
        $type = EmailNotification::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null)
    {
        if(
                ($this->_helper->isDiabledSendingNewCustomerEmail($storeId))
            &&  ($type == EmailNotification::NEW_ACCOUNT_EMAIL_REGISTERED
                    || $type == EmailNotification::NEW_ACCOUNT_EMAIL_CONFIRMED)
        )
        {
            return $subject;
        }

        return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
    }


    /*

    public function beforeSendNewAccountEmail(\Magento\Customer\Model\Customer $subject, $type = 'registered', $backUrl = '', $storeId = '0')
    {
        if($this->_helper->isDiabledSendingNewCustomerEmail($storeId))
        {
            return $subject;
        }
        return;
    }

    public function aroundNewAccount(\Magento\Customer\Model\EmailNotification $subject, \Closure $proceed)
    {
        return $subject;
    }

    public function aroundSendNewAccountEmail(\Magento\Customer\Model\Customer $subject, callable $proceed, $type = 'registered', $backUrl = '', $storeId = '0')
    {
        return $proceed($type, $backUrl, $storeId);
    }
    */
}