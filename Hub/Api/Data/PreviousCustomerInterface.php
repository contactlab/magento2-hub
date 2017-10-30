<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 12:54
 */
namespace Contactlab\Hub\Api\Data;

interface PreviousCustomerInterface
{
    const PREVIOUS_CUSTOMER_ID = 'previous_customer_id';
    const CUSTOMER_ID = 'customer_id';
    const STORE_ID = 'store_id';
    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const REMOTE_IP = 'remote_ip';
    CONST IS_EXPORTED = 'is_exported';


    /**
     * Event Id
     * @return int|null
     */
    public function getId();

    /**
     * Set Previous Customer Id
     * @param int $previousCustomerId
     * @return int
     */
    public function setPreviousCustomerId($previousCustomerId);

    /**
     * Previous Customer Id
     * @return int
     */
    public function getPreviousCustomerId();

    /**
     * Set Customer Id
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Customer Id
     * @return string
     */
    public function getCustomerId();

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
     * Set Identity Email
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Identity Email
     * @return string
     */
    public function getIdentityEmail();

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
     * Set Remote Ip
     * @param string $remoteIp
     * @return $this
     */
    public function setRemoteIp($remoteIp);

    /**
     * Remote Ip
     * @return string|null
     */
    public function getRemoteIp();

    /**
     * Set Is Exported
     * @param int $isExported
     * @return int
     */
    public function setIsExported($isExported);

    /**
     * Is Exported
     * @return int
     */
    public function getIsExported();

}