<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 09:13
 */
namespace Contactlab\Hub\Api\Data;

interface AbandonedCartInterface
{
    const ABANDONED_CART_ID = 'abandoned_cart_id';
    const QUOTE_ID = 'quote_id';
    const STORE_ID = 'store_id';
    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const ABANDONED_AT = 'abandoned_at';
    const REMOTE_IP = 'remote_ip';
    const IS_EXPORTED = 'is_exported';

    const ABANDONED_CART_STATUS_UNEXPORTED = 0;
    const ABANDONED_CART_STATUS_EXPORTED = 1;


    /**
     * Event Id
     * @return int|null
     */
    public function getId();

    /**
     * Set Abandoned Cart Id
     * @param int $abandonedCartId
     * @return int
     */
    public function setAbandonedCartId($abandonedCartId);

    /**
     * Abandoned Cart Id
     * @return int
     */
    public function getAbandonedCartId();

    /**
     * Set Quote Id
     * @param string $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Quote Id
     * @return string
     */
    public function getQuoteId();

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
     * Set Email
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Email
     * @return string
     */
    public function getEmail();

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
     * Set Updated At
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Updated At
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Abandoned At
     * @param string $abandonedAt
     * @return $this
     */
    public function setAbandonedAt($abandonedAt);

    /**
     * Abandoned At
     * @return string|null
     */
    public function getAbandonedAt();

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