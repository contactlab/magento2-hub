<?php
namespace Contactlab\Hub\Model;

use Contactlab\Hub\Api\Data\HubInterface;
use Contactlab\Hub\Helper\Data as HubHelper;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Api\AbstractSimpleObject;

class Hub extends AbstractSimpleObject implements HubInterface
{
    protected $_curlFactory;
    protected $_helper;

    public function __construct(
        CurlFactory $curlFactory,
        HubHelper $helper,
        array $data = []
    )
    {
        $this->_curlFactory = $curlFactory;
        $this->_helper = $helper;
        parent::__construct($data);
    }

    /**
     * Api Token
     * @return int|null
     */
    public function getApiToken()
    {
        return $this->_get(self::API_TOKEN);
    }

    /**
     * Set Api Token
     * @param string $apiToken
     * @return $this
     */
    public function setApiToken($apiToken)
    {
        return $this->setData(self::API_TOKEN, $apiToken);
    }

    /**
     * Api Workspace
     * @return string
     */
    public function getApiWorkspace()
    {
        return $this->_get(self::API_WORKSPACE);
    }

    /**
     * Set Api Workspace
     * @param string $apiWorkspace
     * @return $this
     */
    public function setApiWorkspace($apiWorkspace)
    {
        return $this->setData(self::API_WORKSPACE, $apiWorkspace);
    }

    /**
     * Api Node Id
     * @return string
     */
    public function getApiNodeId()
    {
        return $this->_get(self::API_NODEID);
    }

    /**
     * Set Api Node Id
     * @param string $apiNodeId
     * @return $this
     */
    public function setApiNodeId($apiNodeId)
    {
        return $this->setData(self::API_NODEID, $apiNodeId);
    }

    /**
     * Api Url
     * @return string
     */
    public function getApiUrl()
    {
        return $this->_get(self::API_URL);
    }

    /**
     * Set Api Url
     * @param string $apiUrl
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        return $this->setData(self::API_URL, $apiUrl);
    }

    /**
     * Api Proxy
     * @return int
     */
    public function getApiProxy()
    {
        return $this->_get(self::API_PROXY);
    }

    /**
     * Set Api Proxy
     * @param string $apiProxy
     * @return $this
     */
    public function setApiProxy($apiProxy)
    {
        return $this->setData(self::API_PROXY, $apiProxy);
    }


    public function call(string $url, \stdClass $data = null, $method = \Zend_Http_Client::POST)
    {
        $this->_helper->log(__METHOD__);
        $this->_helper->log($url);
        $this->_helper->log(json_encode($data));
        $this->_helper->log($method);

        $curl = curl_init();
        if ($this->getApiProxy())
        {
            curl_setopt($curl, CURLOPT_PROXY, $this->getApiProxy());
        }
        if ($data)
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if ($method != \Zend_Http_Client::POST)
        {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }
        $header = array(
            'X-Forwarded-Ssl:on',
            'Content-Type:application/json',
            'Authorization: Bearer ' . $this->getApiToken()
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


        $response = curl_exec($curl);

        $this->_helper->log("RESPONSE:");
        $this->_helper->log($response);
        
        if (curl_errno($curl) || $response === false) {
            $response = curl_error($curl);
            curl_close($curl);
            throw new \Exception($response, 667);
        }

        $response = json_decode($response);
        if(!$response)
        {
            $response = new \stdClass();
        }
        $response->curl_http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->_helper->log("FINE RESPONSE: ".$response->curl_http_code);

        $response = json_encode($response);
        return $response;
    }




}