<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */
namespace Contactlab\Hub\Api\Data;

interface HubInterface
{
    const API_VERSION = 'hub/v1/workspaces/';
    const API_TOKEN = 'apitoken';
    const API_WORKSPACE = 'apiworkspace';
    const API_NODEID = 'apinodeid';
    const API_URL = 'apiurl';
    const API_PROXY = 'apiproxy';


    /**
     * Api Token
     * @return int|null
     */
    public function getApiToken();

    /**
     * Set Api Token
     * @param string $apiToken
     * @return $this
     */
    public function setApiToken($apiToken);

    /**
     * Api Workspace
     * @return string
     */
    public function getApiWorkspace();

    /**
     * Set Api Workspace
     * @param string $apiWorkspace
     * @return $this
     */
    public function setApiWorkspace($apiWorkspace);

    /**
     * Api Node Id
     * @return string
     */
    public function getApiNodeId();

    /**
     * Set Api Node Id
     * @param string $apiNodeId
     * @return $this
     */
    public function setApiNodeId($apiNodeId);

    /**
     * Api Url
     * @return string
     */
    public function getApiUrl();

    /**
     * Set Api Url
     * @param string $apiUrl
     * @return $this
     */
    public function setApiUrl($apiUrl);

    /**
     * Api Proxy
     * @return int
     */
    public function getApiProxy();

    /**
     * Set Api Proxy
     * @param string $apiProxy
     * @return $this
     */
    public function setApiProxy($apiProxy);


}