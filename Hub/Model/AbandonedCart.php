<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 09:13
 */

namespace Contactlab\Hub\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Contactlab\Hub\Api\Data\AbandonedCartInterface;

/**
 * @method ${
RESOURCENAME
} getResource()
 * @method ${
RESOURCENAME
}\Collection getCollection()
 */
class AbandonedCart extends AbstractModel implements AbandonedCartInterface, IdentityInterface
{
    const CACHE_TAG = 'contactlab_hub_abandonedcart';
    protected $_cacheTag = 'contactlab_hub_abandonedcart';
    protected $_eventPrefix = 'contactlab_hub_abandonedcart';

    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\ResourceModel\AbandonedCart');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Set Abandoned Cart Id
     * @param int $abandonedCartId
     * @return int
     */
    public function setAbandonedCartId($abandonedCartId)
    {
        return $this->setData(AbandonedCartInterface::ABANDONED_CART_ID, $abandonedCartId);
    }

    /**
     * Abandoned Cart Id
     * @return int
     */
    public function getAbandonedCartId()
    {
        return $this->getData(AbandonedCartInterface::ABANDONED_CART_ID);
    }

    /**
     * Set Quote Id
     * @param string $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(AbandonedCartInterface::QUOTE_ID, $quoteId);
    }

    /**
     * Quote Id
     * @return string
     */
    public function getQuoteId()
    {
        return $this->getData(AbandonedCartInterface::QUOTE_ID);
    }

    /**
     * Set Store Id
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(AbandonedCartInterface::STORE_ID, $storeId);
    }

    /**
     * Store Id
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(AbandonedCartInterface::STORE_ID);
    }

    /**
     * Set Email
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->setData(AbandonedCartInterface::EMAIL, $email);
    }

    /**
     * Email
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(AbandonedCartInterface::EMAIL);
    }

    /**
     * Set Created At
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(AbandonedCartInterface::CREATED_AT, $createdAt);
    }

    /**
     * Created At
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(AbandonedCartInterface::CREATED_AT);
    }

    /**
     * Set Updated At
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(AbandonedCartInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * Updated At
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(AbandonedCartInterface::UPDATED_AT);
    }

    /**
     * Set Abandoned At
     * @param string $abandonedAt
     * @return $this
     */
    public function setAbandonedAt($abandonedAt)
    {
        return $this->setData(AbandonedCartInterface::ABANDONED_AT, $abandonedAt);
    }

    /**
     * Abandoned At
     * @return string|null
     */
    public function getAbandonedAt()
    {
        return $this->getData(AbandonedCartInterface::ABANDONED_AT);
    }

    /**
     * Set Remote Ip
     * @param string $remoteIp
     * @return $this
     */
    public function setRemoteIp($remoteIp)
    {
        return $this->setData(AbandonedCartInterface::REMOTE_IP, $remoteIp);
    }

    /**
     * Remote Ip
     * @return string|null
     */
    public function getRemoteIp()
    {
        return $this->getData(AbandonedCartInterface::REMOTE_IP);
    }

    /**
     * Set Is Exported
     * @param int $isExported
     * @return int
     */
    public function setIsExported($isExported)
    {
        return $this->setData(AbandonedCartInterface::IS_EXPORTED, $isExported);
    }

    /**
     * Is Exported
     * @return int
     */
    public function getIsExported()
    {
        return $this->getData(AbandonedCartInterface::IS_EXPORTED);
    }
}