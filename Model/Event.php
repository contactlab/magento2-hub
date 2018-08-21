<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Contactlab\Hub\Api\Data\EventInterface;

/**
 * @method ${
RESOURCENAME
} getResource()
 * @method ${
RESOURCENAME
}\Collection getCollection()
 */
class Event extends AbstractModel implements EventInterface,
    IdentityInterface
{
    const CACHE_TAG = 'contactlab_hub_event';


    protected $_cacheTag = 'contactlab_hub_event';
    protected $_eventPrefix = 'contactlab_hub_event';

    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\ResourceModel\Event');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Set Event Id
     * @return int
     */
    public function setEventId($eventId)
    {
        return $this->setData(EventInterface::EVENT_ID, $eventId);
    }

    /**
     * Event Id
     * @return string
     */
    public function getEventId()
    {
        return $this->getData(EventInterface::EVENT_ID);
    }

    /**
     * Set Name
     * @param string $name
     * @return EventInterface
     */
    public function setName($name)
    {
        return $this->setData(EventInterface::NAME, $name);
    }

    /**
     * Name
     * @return string
     */
    public function getName()
    {
        return $this->getData(EventInterface::NAME);
    }

    /**
     * Set Identity Email
     * @param string $identityEmail
     * @return EventInterface
     */
    public function setIdentityEmail($identityEmail)
    {
        return $this->setData(EventInterface::IDENTITY_EMAIL, $identityEmail);
    }

    /**
     * Identity Email
     * @return string
     */
    public function getIdentityEmail()
    {
        return $this->getData(EventInterface::IDENTITY_EMAIL);
    }

    /**
     * Set Store Id
     * @param int $storeId
     * @return EventInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(EventInterface::STORE_ID, $storeId);
    }

    /**
     * Store Id
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(EventInterface::STORE_ID);
    }

    /**
     * Set Status
     * @param int $status
     * @return EventInterface
     */
    public function setStatus($status)
    {
        return $this->setData(EventInterface::STATUS, $status);
    }

    /**
     * Status
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(EventInterface::STATUS);
    }

    /**
     * Set Need Update Identity
     * @param bool $needUpdateIdentity
     * @return EventInterface
     */
    public function setNeedUpdateIdentity($needUpdateIdentity)
    {
        return $this->setData(EventInterface::NEED_UPDATE_IDENTITY, $needUpdateIdentity);
    }

    /**
     * Need Update Identity
     * @return bool
     */
    public function getNeedUpdateIdentity()
    {
        return $this->getData(EventInterface::NEED_UPDATE_IDENTITY);
    }

    /**
     * Set Created At
     * @param string $createdAt
     * @return EventInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(EventInterface::CREATED_AT, $createdAt);
    }

    /**
     * Created At
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(EventInterface::CREATED_AT);
    }

    /**
     * Set Exported At
     * @param string $exportedAt
     * @return EventInterface
     */
    public function setExportedAt($exportedAt)
    {
        return $this->setData(EventInterface::EXPORTED_AT, $exportedAt);
    }

    /**
     * Exported At
     * @return string|null
     */
    public function getExportedAt()
    {
        return $this->getData(EventInterface::EXPORTED_AT);
    }

    /**
     * Set Event Data
     * @param string $eventData
     * @return EventInterface
     */
    public function setEventData($eventData)
    {
        return $this->setData(EventInterface::EVENT_DATA, $eventData);
    }

    /**
     * Event Data
     * @return string|null
     */
    public function getEventData()
    {
        return $this->getData(EventInterface::EVENT_DATA);
    }

    /**
     * Set Session Id
     * @param string $sessionId
     * @return EventInterface
     */
    public function setSessionId($sessionId)
    {
        return $this->setData(EventInterface::SESSION_ID, $sessionId);
    }

    /**
     * Session Id
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->getData(EventInterface::SESSION_ID);
    }

    /**
     * Set Hub Customer Id
     * @param string $hubCustomerId
     * @return $this
     */
    public function setHubCustomerId($hubCustomerId)
    {
        return $this->setData(EventInterface::HUB_CUSTOMER_ID, $hubCustomerId);
    }

    /**
     * Hub Customer Id
     * @return string|null
     */
    public function getHubCustomerId()
    {
        return $this->getData(EventInterface::HUB_CUSTOMER_ID);
    }

    /**
     * Set Env User Agent
     * @param string $envUserAgent
     * @return EventInterface
     */
    public function setEnvUserAgent($envUserAgent)
    {
        return $this->setData(EventInterface::ENV_USER_AGENT, $envUserAgent);
    }

    /**
     * Env User Agent
     * @return string|null
     */
    public function getEnvUserAgent()
    {
        return $this->getData(EventInterface::ENV_USER_AGENT);
    }

    /**
     * Set Env Remote Ip
     * @param string $envRemoteIp
     * @return EventInterface
     */
    public function setEnvRemoteIp($envRemoteIp)
    {
        return $this->setData(EventInterface::ENV_REMOTE_IP, $envRemoteIp);
    }

    /**
     * Env Remote Ip
     * @return string|null
     */
    public function getEnvRemoteIp()
    {
        return $this->getData(EventInterface::ENV_REMOTE_IP);
    }

    /**
     * Set Hub Event
     * @param string $hubEvent
     * @return EventInterface
     */
    public function setHubEvent($hubEvent)
    {
        return $this->setData(EventInterface::HUB_EVENT, $hubEvent);
    }

    /**
     * Hub Event
     * @return string|null
     */
    public function getHubEvent()
    {
        return $this->getData(EventInterface::HUB_EVENT);
    }

}