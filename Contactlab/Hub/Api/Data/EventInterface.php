<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */
namespace Contactlab\Hub\Api\Data;

interface EventInterface
{
    const EVENT_ID = 'event_id';
    const NAME = 'name';
    const IDENTITY_EMAIL = 'identity_email';
    const STORE_ID = 'store_id';
    const NEED_UPDATE_IDENTITY = 'need_update_identity';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const EXPORTED_AT = 'exported_at';
    const EVENT_DATA = 'event_data';
    const SESSION_ID = 'session_id';
    const HUB_CUSTOMER_ID = 'hub_customer_id';
    const ENV_USER_AGENT = 'env_user_agent';
    const ENV_REMOTE_IP = 'env_remote_ip';
    const HUB_EVENT = 'hub_event';

    const EVENT_STATUS_UNEXPORTED = 0;
    const EVENT_STATUS_RETRY = 1;
    const EVENT_STATUS_RUNNING = 2;
    const EVENT_STATUS_EXPORTED = 3;
    const EVENT_STATUS_ERROR= -1;

    /**
     * Event Id
     * @return int|null
     */
    public function getId();

    /**
     * Set Event Id
     * @return int
     */
    public function setEventId($eventId);

    /**
     * Event Id
     * @return int
     */
    public function getEventId();

    /**
     * Set Name
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Name
     * @return string
     */
    public function getName();

    /**
     * Set Identity Email
     * @param string $identityEmail
     * @return $this
     */
    public function setIdentityEmail($identityEmail);

    /**
     * Identity Email
     * @return string
     */
    public function getIdentityEmail();

    /**
     * Set Store Id
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Store Id
     * @return int
     */
    public function getStoreId();

    /**
     * Set Status
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Status
     * @return int
     */
    public function getStatus();

    /**
     * Set Need Update Identity
     * @param bool $needUpdateIdentity
     * @return $this
     */
    public function setNeedUpdateIdentity($needUpdateIdentity);

    /**
     * Need Update Identity
     * @return bool
     */
    public function getNeedUpdateIdentity();

    /**
     * Set Created At
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Created At
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set Exported At
     * @param string $exportedAt
     * @return $this
     */
    public function setExportedAt($exportedAt);

    /**
     * Exported At
     * @return string|null
     */
    public function getExportedAt();

    /**
     * Set Event Data
     * @param string $eventData
     * @return $this
     */
    public function setEventData($eventData);

    /**
     * Event Data
     * @return string|null
     */
    public function getEventData();

    /**
     * Set Session Id
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId);

    /**
     * Session Id
     * @return string|null
     */
    public function getSessionId();

    /**
     * Set Hub Customer Id
     * @param string $hubCustomerId
     * @return $this
     */
    public function setHubCustomerId($hubCustomerId);

    /**
     * Hub Customer Id
     * @return string|null
     */
    public function getHubCustomerId();

    /**
     * Set Env User Agent
     * @param string $envUserAgent
     * @return $this
     */
    public function setEnvUserAgent($envUserAgent);

    /**
     * Env User Agent
     * @return string|null
     */
    public function getEnvUserAgent();

    /**
     * Set Env Remote Ip
     * @param string $envRemoteIp
     * @return $this
     */
    public function setEnvRemoteIp($envRemoteIp);

    /**
     * Env Remote Ip
     * @return string|null
     */
    public function getEnvRemoteIp();

    /**
     * Set Hub Event
     * @param string $hubEvent
     * @return $this
     */
    public function setHubEvent($hubEvent);

    /**
     * Hub Event
     * @return string|null
     */
    public function getHubEvent();
}