<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 12:54
 */

namespace Contactlab\Hub\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Contactlab\Hub\Api\Data\PreviousCustomerInterface;

/**
 * @method ${
RESOURCENAME
} getResource()
 * @method ${
RESOURCENAME
}\Collection getCollection()
 */
class PreviousCustomer extends AbstractModel implements PreviousCustomerInterface,
    IdentityInterface
{
    const CACHE_TAG = 'contactlab_hub_previouscustomer';
    protected $_cacheTag = 'contactlab_hub_previouscustomer';
    protected $_eventPrefix = 'contactlab_hub_previouscustomer';

    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\ResourceModel\PreviousCustomer');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Set Previous Customer Id
     * @param int $previousCustomerId
     * @return int
     */
    public function setPreviousCustomerId($previousCustomerId)
    {
        return $this->setData(PreviousCustomerInterface::PREVIOUS_CUSTOMER_ID, $previousCustomerId);
    }

    /**
     * Previous Customer Id
     * @return int
     */
    public function getPreviousCustomerId()
    {
        return $this->getData(PreviousCustomerInterface::PREVIOUS_CUSTOMER_ID);
    }

    /**
     * Set Customer Id
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(PreviousCustomerInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * Customer Id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(PreviousCustomerInterface::CUSTOMER_ID);
    }

    /**
     * Set Store Id
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(PreviousCustomerInterface::STORE_ID, $storeId);
    }

    /**
     * Store Id
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(PreviousCustomerInterface::STORE_ID);
    }

    /**
     * Set Identity Email
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->setData(PreviousCustomerInterface::EMAIL, $email);
    }

    /**
     * Identity Email
     * @return string
     */
    public function getIdentityEmail()
    {
        return $this->getData(PreviousCustomerInterface::EMAIL);
    }

    /**
     * Set Created At
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(PreviousCustomerInterface::CREATED_AT, $createdAt);
    }

    /**
     * Created At
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(PreviousCustomerInterface::CREATED_AT);
    }

    /**
     * Set Remote Ip
     * @param string $remoteIp
     * @return $this
     */
    public function setRemoteIp($remoteIp)
    {
        return $this->setData(PreviousCustomerInterface::REMOTE_IP, $remoteIp);
    }

    /**
     * Remote Ip
     * @return string|null
     */
    public function getRemoteIp()
    {
        return $this->getData(PreviousCustomerInterface::REMOTE_IP);
    }

    /**
     * Set Is Exported
     * @param int $isExported
     * @return int
     */
    public function setIsExported($isExported)
    {
        return $this->setData(PreviousCustomerInterface::IS_EXPORTED, $isExported);
    }

    /**
     * Is Exported
     * @return int
     */
    public function getIsExported()
    {
        return $this->getData(PreviousCustomerInterface::IS_EXPORTED);
    }
}