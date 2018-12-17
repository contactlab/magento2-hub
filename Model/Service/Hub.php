<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Service;

use Contactlab\Hub\Api\HubManagementInterface;
use Contactlab\Hub\Api\Data\HubInterface;
use Contactlab\Hub\Api\Data\EventInterface;
use Contactlab\Hub\Helper\Data as HubHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\State;

class Hub implements HubManagementInterface
{
    protected $_hubModel;
    protected $_hub;
    protected $_helper;
    protected $_objectManager;
    protected $_customerFactory;
    protected $_addressRepository;
    protected $_countryFactory;
    protected $_subscriberFactory;
    protected $_storeId;
    private $_subscriptionEvents = array('campaignSubscribed', 'campaignUnsubscribed');


    public function __construct(
        HubInterface $hubModel,
        HubHelper $helper,
        ObjectManagerInterface $objectManager,
        CustomerFactory $customerFactory,
        AddressRepositoryInterface $addressRepository,
        CountryFactory $countryFactory,
        SubscriberFactory $subscriberFactory,
        State $state
    ){
        $this->_hubModel = $hubModel;
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_customerFactory = $customerFactory;
        $this->_addressRepository = $addressRepository;
        $this->_countryFactory = $countryFactory;
        $this->_subscriberFactory = $subscriberFactory;
        //$state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
    }

    /**
     * Init Hub
     *
     * @param int $storeId
     * @return $this
     */
    public function initHub($storeId)
    {

        return $this;
    }

    /**
     * Set Store Id
     *
     * @param $storeId
     * @return $this
     *
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    protected function _getHub()
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        if(!$this->_hub)
        {
            $apiToken = $this->_helper->getApiToken($this->_storeId);
            $apiWorkspace = $this->_helper->getApiWorkspaceId($this->_storeId);
            $apiNodeId = $this->_helper->getApiNodeId($this->_storeId);
            $apiUrl = $this->_helper->getApiUrl($this->_storeId);
            $apiProxy = $this->_helper->useProxy($this->_storeId) ? $this->_helper->getProxy($this->_storeId) : null;

            $this->_helper->log('STORE ID: '.$this->_storeId);
            $this->_helper->log('API TOKEN: '.$apiToken);
            $this->_helper->log('API WORKSPACE: '.$apiWorkspace);
            $this->_helper->log('API NODE ID: '.$apiNodeId);
            $this->_helper->log('API URL: '.$apiUrl);
            $this->_helper->log('API PROXY: '.$apiProxy);

            var_dump('STORE ID: '.$this->_storeId);
            var_dump('API TOKEN: '.$apiToken);
            var_dump('API WORKSPACE: '.$apiWorkspace);
            var_dump('API NODE ID: '.$apiNodeId);
            var_dump('API URL: '.$apiUrl);
            var_dump('API PROXY: '.$apiProxy);

            $this->_hub = $this->_hubModel;
            $this->_hub->setApiToken($apiToken)
                ->setApiWorkspace($apiWorkspace)
                ->setApiNodeId($apiNodeId)
                ->setApiUrl($apiUrl)
                ->setApiProxy($apiProxy);
        }
        return $this->_hub;
    }

    /**
     * Compose Event
     *
     * @param EventInterface $event
     * @return \stdClass
     */
    public function composeHubEvent(EventInterface $event)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $hubEvent = $this->_objectManager
            ->create('\Contactlab\Hub\Model\Hub\Strategy\\'. ucfirst($event->getName()))
            ->setEvent($event)
            ->build();

        if(!$hubEvent)
        {
            $hubEvent = new \stdClass();
        }

        $hubEvent->type = $event->getName();
        $hubEvent->date = date(DATE_ISO8601, strtotime($event->getCreatedAt()));
        $hubEvent->context = $this->_helper->getContext();
        $store = $this->_helper->getStore($event->getStoreId());
        $contextInfo = new \stdClass();
        $objStore = new \stdClass();
        $objStore->id = "".$event->getStoreId();
        $objStore->name = $store->getFrontendName();
        $objStore->country = "".$this->_helper->getStoreDefaultCountry($this->_storeId);
        $objStore->website = $store->getBaseUrl();
        $objStore->type = $this->_helper->getContext();
        $contextInfo->store = $objStore;
        $client = new \stdClass();
        if($event->getEnvUserAgent())
        {
            $client->userAgent = "".$event->getEnvUserAgent();
        }
        if($event->getEnvRemoteIp())
        {
            $client->ip = "".$event->getEnvRemoteIp();
        }
        $contextInfo->client = $client;
        $hubEvent->contextInfo = $contextInfo;

        if($event->getHubCustomerId())
        {
            $hubEvent->customerId = $event->getHubCustomerId();
        }
        else
        {
            $bringBackProperties = new \stdClass();
            $bringBackProperties->type = "SESSION_ID";
            $bringBackProperties->value = $event->getSessionId();
            $bringBackProperties->nodeId = $this->_helper->getApiNodeId($this->_storeId);
            $hubEvent->bringBackProperties = $bringBackProperties;
        }

        return $hubEvent;
    }


    /**
     * Compose Event
     *
     * @param EventInterface $event
     * @return \stdClass
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function composeHubCustomer(EventInterface $event)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $hubCustomer = new \stdClass();
        $hubCustomer->nodeId = $this->_helper->getApiNodeId($event->getStoreId());
        $base = new \stdClass();
        $contacts = new \stdClass();
        $contacts->email = $event->getIdentityEmail();
        $base->contacts = $contacts;
        $locale = $this->_helper->getStoreDefaultLocale($event->getStoreId());
        if ($locale)
        {
            $base->locale = $locale;
        }
        $websiteId = $this->_helper->getStore($event->getStoreId())->getWebsiteId();        
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($websiteId)
        	->loadByEmail($event->getIdentityEmail());        
        if ($customer)
        {
            $customerAddressId = $customer->getDefaultBilling();
            if ($customerAddressId)
            {
                $objAddress = new \stdClass();
                $address = $this->_addressRepository->getById($customerAddressId);
                if ($address->getCity())
                {
                    $objAddress->city = $address->getCity();
                }
                $street = '';
                foreach ($address->getStreet() as $piece)
                {
                    $street.= $piece.' ';
                }
                $objAddress->street = trim($street);
                if ($address->getRegion()->getRegion())
                {
                    $objAddress->province = $address->getRegion()->getRegion();
                }
                if ($address->getPostcode())
                {
                    $objAddress->zip = $address->getPostcode();
                }
                if ($address->getCountryId())
                {
                    $country = $this->_countryFactory->create()->loadByCode($address->getCountryId())->getName();
                    $objAddress->country = $country ?: '';
                }
                $base->address = $objAddress;
            }
            $extraBaseProperties = $this->_helper->getCustomerExtraProperties($customer, 'base');
            $base = (object) array_merge( (array)$base, $extraBaseProperties );

            $hubCustomer->externalId = $this->_helper->getExternalId($customer);

            $extraExtendedProperties = $this->_helper->getCustomerExtraProperties($customer, 'extended');
            if (count($extraExtendedProperties) > 0)
            {
                $hubCustomer->extended = (object) $extraExtendedProperties;
            }

            $extraConsentsProperties = $this->_helper->getCustomerExtraProperties($customer, 'consents');
            if (count($extraConsentsProperties) > 0) {
                $hubCustomer->consents = (object) $extraConsentsProperties;
            }
        }

        if($this->_checkSubscription($event))
        {
            $subscriber = $this->_subscriberFactory->create()->loadByEmail($event->getIdentityEmail());
            if ($subscriber->getId())
            {
                $subscriberObj = new \stdClass();
                $subscriberObj->id = $this->_helper->getCampaignName($this->_storeId);
                $subscriberObj->kind = "DIGITAL_MESSAGE";
                $subscriberObj->subscribed = ($subscriber->getSubscriberStatus() == Subscriber::STATUS_SUBSCRIBED)
                    ? true : false;
                $subscriberObj->subscriberId = $subscriber->getSubscriberId();
                $subscriberObj->updatedAt = date(DATE_ISO8601, strtotime($event->getCreatedAt()));
                $subscriberObj->registeredAt = date(DATE_ISO8601, strtotime($subscriber->getCreatedAt()));
                if($subscriberObj->subscribed)
                {
                    $subscriberObj->startDate = date(DATE_ISO8601, strtotime($subscriber->getLastSubscribedAt()));
                    $subscriberObj->endDate = null;
                }
                else
                {
                    $subscriberObj->startDate = null;
                    $subscriberObj->endDate = date(DATE_ISO8601, strtotime($event->getCreatedAt()));
                }
                $subscriptions[] = $subscriberObj;
                $base->subscriptions = $subscriptions;

                if(!$hubCustomer->externalId)
                {
                    $hubCustomer->externalId = $subscriber->getSubscriberEmail();
                }
            }
        }
        $hubCustomer->base = $base;

        var_dump($hubCustomer);
        
        return $hubCustomer;

    }


    /**
     * Send Event
     *
     * @param \stdClass $event
     * @return \stdClass
     */
    public function postEvent(\stdClass $event)
    {
        var_dump($event);
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $url = $this->_getUrl('events');
        $response = $this->_getHub()->call($url, $event);
        $response = json_decode($response);
        $this->_checkError($response);
        return $response;
    }

    /**
     * Update Customer
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function updateCustomer(EventInterface $event)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $hubCustomerId = null;
        if($event->getNeedUpdateIdentity())
        {
            $hubCustomer = $this->composeHubCustomer($event);
            $response = $this->postCustomer($hubCustomer);
            var_dump($response);
            if ($response->curl_http_code == 409) /** Conflicting with customer id */
            {
                if($response->data->customer)
                {
                    unset($hubCustomer->nodeId);
                    $hubCustomer->id = $response->data->customer->id;
                    $url = $response->data->customer->href;
                    $response = $this->patchCustomer($hubCustomer, $url);
                    $hubCustomerId = $response->id;
                }
                else
                {
                    $this->_helper->log('CAN\'T UPDATE CUSTOMER UNTRUSTED SOURCE');
                }
            }

            if($event->getSessionId())
            {
                $session = new \stdClass();
                $session->value = $event->getSessionId();
                $session->id = $hubCustomerId;
                $this->postSession($session);
            }

        }
        return $hubCustomerId;
    }

    /**
     * Post Customer
     *
     * @param \stdClass $event
     * @return \stdClass
     */
    public function postCustomer(\stdClass $customer)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $url = $this->_getUrl('customers');
        $response = $this->_getHub()->call($url, $customer);
        $response = json_decode($response);
        return $response;
    }

    /**
     * Patch Customer
     *
     * @param \stdClass $event
     * @param string $url
     * @return \stdClass
     */
    public function patchCustomer(\stdClass $customer, string $url)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $response = $this->_getHub()->call($url, $customer, \Zend_Http_Client::PATCH);
        $response = json_decode($response);
        return $response;
    }


    /**
     * Post Session
     *
     * @param \stdClass $session
     * @return \stdClass
     */
    public function postSession(\stdClass $session)
    {
        $this->_helper->log(__METHOD__);
        $url = $this->_getUrl('customers/'.$session->id.'/sessions');
        unset($session->id);
        $response = $this->_getHub()->call($url, $session);
        $response = json_decode($response);
        return $response;
    }


    private function _checkSubscription(EventInterface $event)
    {
        return in_array($event->getName(), $this->_subscriptionEvents);
    }


    private function _checkError($response)
    {
        var_dump(__METHOD__);
        $this->_helper->log(__METHOD__);
        $errorMessage = null;
        var_dump($response);

        if($response->curl_http_code == 400 /** Bad Request */)
        {
            if (property_exists($response, 'errors'))
            {
                foreach ($response->errors as $error)
                {
                    $this->_helper->log($error->message);
                }
            }
            $errorMessage = 'Something went wrong.';
            throw new \BadMethodCallException($errorMessage);
        }
        if($response->curl_http_code == 500 /** Internal Error */)
        {
            if (property_exists($response, 'message'))
            {
               $this->_helper->log($response->message);
                $errorMessage = $response->message;
            }
            else
            {
                $errorMessage = 'Something went wrong.';
            }
            throw new \RuntimeException($errorMessage);
        }
        return $this;
    }


    /**
     * Get Url
     *
     * @param string $actionUrl
     * @return string
     */
    private function _getUrl(string $actionUrl)
    {
        $apiUrl = $this->_helper->getApiUrl($this->_storeId);
        if (substr($apiUrl, -1) != '/')
        {
            $apiUrl .= '/';
        }
        return $apiUrl.HubInterface::API_VERSION.$this->_helper->getApiWorkspaceId($this->_storeId).'/'.$actionUrl;
    }
}
