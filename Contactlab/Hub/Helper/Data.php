<?php
namespace Contactlab\Hub\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
//use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Address;


class Data extends AbstractHelper
{
    const MAGENTO_GENERAL_COUNTRY_DEFAULT = 'general/country/default';
    const MAGENTO_GENERAL_LOCALE_DEFAULT = 'general/locale/code';
    const CONTACTLAB_HUB_LOG_ENABLED = 'contactlab_hub/logging/log';
    const CONTACTLAB_HUB_LOG_FILENAME = 'contactlab_hub/logging/logfilename';
    const CONTACTLAB_HUB_API_WORKSPACE_ID = 'contactlab_hub/settings/apiworkspaceid';
    const CONTACTLAB_HUB_API_NODE_ID = 'contactlab_hub/settings/apinodeid';
    const CONTACTLAB_HUB_API_TOKEN = 'contactlab_hub/settings/apitoken';
    const CONTACTLAB_HUB_API_URL = 'contactlab_hub/settings/apiurl';
    const CONTACTLAB_HUB_API_USEPROXY = 'contactlab_hub/settings/useproxy';
    const CONTACTLAB_HUB_API_PROXY = 'contactlab_hub/settings/proxy';
    const CONTACTLAB_HUB_CONTEXT = 'ECOMMERCE';
    const CONTACTLAB_HUB_EVENTS = 'contactlab_hub/events';
    const CONTACTLAB_HUB_EVENTS_ORDER_STATUS = 'contactlab_hub/events/order_status';
    const CONTACTLAB_HUB_CAMPAIGN_NAME = 'contactlab_hub/events/campaignName';
    const CONTACTLAB_HUB_ABANDONED_CART_TO_NOT_SUBSCRIBED = 'contactlab_hub/abandoned_cart/send_to_not_subscribed';
    const CONTACTLAB_HUB_MIN_MINUTES_FROM_LAST_UPDATE = 'contactlab_hub/abandoned_cart/min_minutes_from_last_update';
    const CONTACTLAB_HUB_MAX_MINUTES_FROM_LAST_UPDATE = 'contactlab_hub/abandoned_cart/max_minutes_from_last_update';
    const CONTACTLAB_HUB_CRON_EVENT_LIMIT = 'contactlab_hub/cron_events/limit';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_ENABLED = 'contactlab_hub/cron_previous_customers/enabled';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_PREVIOUS_DATE = 'contactlab_hub/cron_previous_customers/previous_date';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_LIMIT = 'contactlab_hub/cron_previous_customers/limit';
    const CONTACTLAB_HUB_EXCHANGE_RATES_BASE_CURRENCY = 'contactlab_hub/exchange_rates/base_currency';
    const CONTACTLAB_HUB_EXCHANGE_RATES_WEBSITE_CURRENCY = 'contactlab_hub/exchange_rates/website_currency';
    const CONTACTLAB_HUB_EXCHANGE_RATES_EXCHANGE_RATE = 'contactlab_hub/exchange_rates/exchange_rate';
    const CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_HUB = 'contactlab_hub/customer_extra_properties/hub_attribute';
    const CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_MAGE = 'contactlab_hub/customer_extra_properties/mage_attribute';


    protected $_scopeConfig;
    protected $_resourceConfig;
    protected $_storeManager;
    protected $_quoteCollection;
    protected $_eavAttributeRepository;
    //protected $_addressRepository;
    protected $_address;


    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ConfigInterface  $resourceConfig,
        StoreManagerInterface $storeManager,
        QuoteCollectionFactory $quoteCollection,
        AttributeRepositoryInterface $eavAttributeRepository,
        //AddressRepositoryInterface $addressRepository,
        Address $address

    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_storeManager = $storeManager;
        $this->_quoteCollection = $quoteCollection;
        $this->_eavAttributeRepository = $eavAttributeRepository;
        //$this->_addressRepository = $addressRepository;
        $this->_address = $address;
        parent::__construct($context);
    }

    public function log($text)
    {
        if($this->_isLogEnabled()) {
            $log_file = BP . '/var/log/'.$this->_getLogFilename();
            $writer = new \Zend\Log\Writer\Stream($log_file);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($text);
        }
        return $this;
    }
    
    protected function _isLogEnabled($storeId = null)
    {
        return (bool)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_LOG_ENABLED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    protected function _getLogFilename($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_LOG_FILENAME,
            ScopeInterface::SCOPE_STORE, $storeId);
    }


    public function getApiWorkspaceId($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_WORKSPACE_ID,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiNodeId($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_NODE_ID,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiToken($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_TOKEN,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiUrl($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_URL,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function useProxy($storeId = null)
    {
        return (bool)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_USEPROXY,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getProxy($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_API_PROXY,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreDefaultCountry($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::MAGENTO_GENERAL_COUNTRY_DEFAULT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreDefaultLocale($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::MAGENTO_GENERAL_LOCALE_DEFAULT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getContext()
    {
        return self::CONTACTLAB_HUB_CONTEXT;
    }

    public function isEnableEvent(string $eventName, $storeId = null)
    {
        return (bool)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_EVENTS.'/'.$eventName,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getCampaignName($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CAMPAIGN_NAME,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function clearStrings(string $text)
    {
        return trim(str_replace("''", "", str_replace("\n", " ",strip_tags($text))));
    }

    public function getStore($id = null)
    {
        return $this->_storeManager->getStore($id);
    }

    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public function getRemoteIpAddress($customerId = null)
    {
        $remoteIp = null;
        if($customerId)
        {
            $collectionFactory = $this->_quoteCollection->create();
            $collectionFactory->addFieldToSelect(array('remote_ip'));
            $collectionFactory->addFieldToFilter('customer_id', array('eq' => $customerId));
            $collectionFactory->getSelect()->limit(1);
            $collectionFactory->getSelect()->order('created_at');
            echo $collectionFactory->getSelect();
            $remoteIp = $collectionFactory->getFirstItem()->getRemoteIp();
        }
        if(!$remoteIp)
        {
            if (!empty($_SERVER['HTTP_CLIENT_IP']))
            {
                $remoteIp =  $_SERVER['HTTP_CLIENT_IP'];
            }
            else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $remoteIp =  trim($ips[count($ips) - 1]); //real IP address behind proxy IP
            }
            else
            {
                $remoteIp =  $_SERVER['REMOTE_ADDR']; //no proxy found
            }
        }
        return $remoteIp;


    }
    
    public function sendAbandonedCartToNotSubscribed($storeId = null)
    {
        return (bool)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_ABANDONED_CART_TO_NOT_SUBSCRIBED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMinMinutesBeforeSendAbandonedCart($storeId = null)
    {
        return (int)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_MIN_MINUTES_FROM_LAST_UPDATE,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMaxMinutesBeforeSendAbandonedCart($storeId = null)
    {
        return (int)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_MAX_MINUTES_FROM_LAST_UPDATE,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isEnabledPreviousCustomer($storeId = null)
    {
        return (bool)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_ENABLED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPreviousDate($storeId = null)
    {
        $date = $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_PREVIOUS_DATE,
            ScopeInterface::SCOPE_STORE, $storeId);
        if($date)
        {
            $date = date('Y-m-d', strtotime($date));
        }
        return $date;
    }

    public function getEventPageSize($storeId = null)
    {
        return (int)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_EVENT_LIMIT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPreviousCustomerPageSize($storeId = null)
    {
        return (int)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_LIMIT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function setPreviousDate($date, $storeId = Store::DEFAULT_STORE_ID)
    {
        $this->_saveConfig(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_PREVIOUS_DATE, $date, $storeId);
    }

    public function setIsEnabledPreviousCustomer($enabled, $storeId = Store::DEFAULT_STORE_ID)
    {
        $this->_saveConfig(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_ENABLED, $enabled, $storeId);
    }

    public function getOrderStatusToBeSent($storeId = null)
    {
        return explode(',', $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_EVENTS_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE, $storeId));
    }

    protected function _saveConfig($path, $value, $scopeId)
    {
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        if($scopeId != Store::DEFAULT_STORE_ID)
        {
            $scope = 'stores';
        }
        $this->_resourceConfig->saveConfig($path, $value, $scope, $scopeId);
        return $this;
    }

    public function getExchangeRate($storeId = null)
    {
        $exchangeRate = 1;
        $baseCurrency = $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_BASE_CURRENCY,
            ScopeInterface::SCOPE_STORE, $storeId);
        $websiteCurrency = $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_WEBSITE_CURRENCY,
            ScopeInterface::SCOPE_STORE, $storeId);

        if($baseCurrency != $websiteCurrency)
        {
            $exchangeRate = (float)$this->_scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_EXCHANGE_RATE,
                ScopeInterface::SCOPE_STORE, $storeId);
            if(!$exchangeRate)
            {
                $exchangeRate = 1;
            }
        }
        return $exchangeRate;
    }

    public function convertToBaseRate($price, $exchangeRate)
    {
        return round(((float)$price / $exchangeRate) ,2);
    }

    public function getCustomerExtraProperties($customer)
    {
        $extraProperties = array();
        for($i=1; $i<6; $i++)
        {
            $hubAttribute = $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_HUB.'_'.$i);
            $mageAttribute = $this->_scopeConfig->getValue(self::CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_MAGE.'_'.$i);
            if($hubAttribute && $mageAttribute)
            {
                $value = $this->_getCustomerAttributeValue($mageAttribute, $customer);
                $extraProperties[$hubAttribute] = $value;
            }
        }
        return $extraProperties;
    }


    protected function _getCustomerAttributeValue($attributeCode, $customer)
    {
        $value = null;
        if($customer)
        {
            try {
                $customerAttribute = $this->_eavAttributeRepository->get(\Magento\Customer\Model\Customer::ENTITY, $attributeCode);
                if ($customerAttribute->getFrontendInput() == 'select' || $customerAttribute->getFrontendInput() == 'multiselect')
                {
                    $value = ''.$customerAttribute->getSource()->getOptionText($customer->getData($attributeCode));
                }
                else
                {
                    $value = $customer->getData($attributeCode);
                }
            }
            catch(\Magento\Framework\Exception\NoSuchEntityException $e)
            {
                $customerAddressId = $customer->getDefaultBilling();
                if ($customerAddressId) {
                    $address = $this->_address->load($customerAddressId);
                    /** USE REPOSITORY IS BETTER BUT WE HAVEN'T ALL ATTRIBUTES
                    $add = $this->_addressRepository->getById($customerAddressId);
                    var_dump($add->__toArray());
                    die();
                    */
                    $addressAttribute = $this->_eavAttributeRepository->get(\Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode);
                    if ($addressAttribute->getFrontendInput() == 'select' || $addressAttribute->getFrontendInput() == 'multiselect') {
                        $value = '' . $addressAttribute->getSource()->getOptionText($address->getData($attributeCode));
                    } else {
                        $value = $address->getData($attributeCode);
                    }
                }
            }
            catch (\Exception $e)
            {

            }
        }
        return $value;
    }

    public function getMonthsToClean()
    {
        return 1;
    }
}