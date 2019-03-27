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
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;


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
    const CONTACTLAB_HUB_CRON_EVENT_DELETE = 'contactlab_hub/cron_events/delete';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_ENABLED = 'contactlab_hub/cron_previous_customers/enabled';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_EXPORT_ORDERS = 'contactlab_hub/cron_previous_customers/export_orders';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_PREVIOUS_DATE = 'contactlab_hub/cron_previous_customers/previous_date';
    const CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_LIMIT = 'contactlab_hub/cron_previous_customers/limit';
    const CONTACTLAB_HUB_EXCHANGE_RATES_BASE_CURRENCY = 'contactlab_hub/exchange_rates/base_currency';
    const CONTACTLAB_HUB_EXCHANGE_RATES_WEBSITE_CURRENCY = 'contactlab_hub/exchange_rates/website_currency';
    const CONTACTLAB_HUB_EXCHANGE_RATES_EXCHANGE_RATE = 'contactlab_hub/exchange_rates/exchange_rate';
    const CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_EXTERNAL_ID = 'contactlab_hub/customer_extra_properties/external_id';
    const CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_ATTRIBUTE_MAP = 'contactlab_hub/customer_extra_properties/attribute_map';
    const CONTACTLAB_HUB_CAN_SEND_ANONIMOUS_EVENTS = 'contactlab_hub/behavior/send_anonymous';
    const CONTACTLAB_HUB_DISABLE_SENDING_SUBSCRIPTION_EMAIL = 'contactlab_hub/behavior/disable_sending_subscription_email';
    const CONTACTLAB_HUB_DISABLE_SENDING_NEW_CUSTOMER_EMAIL = 'contactlab_hub/behavior/disable_sending_new_customer_email';
    const CONTACTLAB_HUB_JS_TRACKING_ENABLED = 'contactlab_hub/js_tracking/enabled';
    const CONTACTLAB_HUB_JS_UNTRUSTED_TOKEN = 'contactlab_hub/js_tracking/untrusted_token';


    protected $_resourceConfig;
    protected $_storeManager;
    protected $_quoteCollection;
    protected $_eavAttributeRepository;
    //protected $_addressRepository;
    protected $_address;
    protected $_imageHelper;
    protected $_urlBuilder;
    protected $_categoryRepository;
    protected $_serializer;


    public function __construct(
        Context $context,
        ConfigInterface  $resourceConfig,
        StoreManagerInterface $storeManager,
        QuoteCollectionFactory $quoteCollection,
        AttributeRepositoryInterface $eavAttributeRepository,
        //AddressRepositoryInterface $addressRepository,
        Address $address,
        ImageHelper $imageHelper,
        UrlInterface $urlBuilder,
        CategoryRepositoryInterface $categoryRepository,
        Json $serializer = null

    ) {
        $this->_resourceConfig = $resourceConfig;
        $this->_storeManager = $storeManager;
        $this->_quoteCollection = $quoteCollection;
        $this->_eavAttributeRepository = $eavAttributeRepository;
        //$this->_addressRepository = $addressRepository;
        $this->_address = $address;
        $this->_imageHelper = $imageHelper;
        $this->_urlBuilder = $urlBuilder;
        $this->_categoryRepository = $categoryRepository;
        $this->_serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
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
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_LOG_ENABLED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    protected function _getLogFilename($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_LOG_FILENAME,
            ScopeInterface::SCOPE_STORE, $storeId);
    }


    public function getApiWorkspaceId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_WORKSPACE_ID,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiNodeId($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_NODE_ID,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiToken($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_TOKEN,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiUrl($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_URL,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function useProxy($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_USEPROXY,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getProxy($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_API_PROXY,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreDefaultCountry($storeId = null)
    {
        return $this->scopeConfig->getValue(self::MAGENTO_GENERAL_COUNTRY_DEFAULT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreDefaultLocale($storeId = null)
    {
        return $this->scopeConfig->getValue(self::MAGENTO_GENERAL_LOCALE_DEFAULT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getContext()
    {
        return self::CONTACTLAB_HUB_CONTEXT;
    }

    public function isEnableEvent(string $eventName, $storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_EVENTS.'/'.$eventName,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getCampaignName($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_CAMPAIGN_NAME,
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
            elseif(!empty($_SERVER['REMOTE_ADDR']))
            {
                $remoteIp = $_SERVER['REMOTE_ADDR']; //no proxy found
            }
        }
        return $remoteIp;


    }
    
    public function sendAbandonedCartToNotSubscribed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_ABANDONED_CART_TO_NOT_SUBSCRIBED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMinMinutesBeforeSendAbandonedCart($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_MIN_MINUTES_FROM_LAST_UPDATE,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getMaxMinutesBeforeSendAbandonedCart($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_MAX_MINUTES_FROM_LAST_UPDATE,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isEnabledPreviousCustomer($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_ENABLED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function canExportPreviousOrders($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_EXPORT_ORDERS,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPreviousDate($storeId = null)
    {
        $date = $this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_PREVIOUS_DATE,
            ScopeInterface::SCOPE_STORE, $storeId);
        if($date)
        {
            $date = date('Y-m-d', strtotime($date));
        }
        return $date;
    }

    public function getEventPageSize($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_EVENT_LIMIT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPreviousCustomerPageSize($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_LIMIT,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function setExportPreviousOrders($enabled = false, $storeId = Store::DEFAULT_STORE_ID)
    {
        $this->_saveConfig(self::CONTACTLAB_HUB_CRON_PREVIOUS_CUSTOMERS_EXPORT_ORDERS, $enabled, $storeId);
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
        return explode(',', $this->scopeConfig->getValue(self::CONTACTLAB_HUB_EVENTS_ORDER_STATUS,
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

    public function getMonthsToClean($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CRON_EVENT_DELETE,
            ScopeInterface::SCOPE_STORE, $storeId);
    }


    public function canSendAnonymousEvents($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_CAN_SEND_ANONIMOUS_EVENTS,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isDiabledSendingSubscriptionEmail($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_DISABLE_SENDING_SUBSCRIPTION_EMAIL,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isDiabledSendingNewCustomerEmail($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_DISABLE_SENDING_NEW_CUSTOMER_EMAIL,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isJsTrackingEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_JS_TRACKING_ENABLED,
            ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiTokenForJavascript($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONTACTLAB_HUB_JS_UNTRUSTED_TOKEN,
            ScopeInterface::SCOPE_STORE, $storeId) ?: $this->getApiToken($storeId);
    }

    public function getExchangeRate($storeId = null)
    {
        $exchangeRate = 1;
        $baseCurrency = $this->scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_BASE_CURRENCY,
            ScopeInterface::SCOPE_STORE, $storeId);
        $websiteCurrency = $this->scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_WEBSITE_CURRENCY,
            ScopeInterface::SCOPE_STORE, $storeId);

        if($baseCurrency != $websiteCurrency)
        {
            $exchangeRate = (float)$this->scopeConfig->getValue(self::CONTACTLAB_HUB_EXCHANGE_RATES_EXCHANGE_RATE,
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

    public function getExternalId($customer)
    {
        $externalId = $this->scopeConfig->getValue(
            self::CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_EXTERNAL_ID,
            ScopeInterface::SCOPE_STORE,
            $customer->getStoreId()
        );
        return $this->_getCustomerAttributeValue($externalId, $customer);
    }

    public function getCustomerExtraProperties($customer, $type = 'extended')
    {
        $extraProperties = array();
        $attributesMap = $this->_serializer->unserialize($this->scopeConfig->getValue(
            self::CONTACTLAB_HUB_CUSTOMER_EXTRA_PROPERTIES_ATTRIBUTE_MAP,
            ScopeInterface::SCOPE_STORE,
            $customer->getStoreId()
        ));
        foreach ($attributesMap as $map)
        {
            if($type == $map['hub_type'])
            {
                $value = $this->_getCustomerAttributeValue($map['magento_attribute'], $customer);
                if(!is_null($value))
                {
                    $extraProperties[$map['hub_attribute']] = $value;
                }
            }
        }
        return $extraProperties;
    }


    protected function _getCustomerAttributeValue($attributeCode, $customer)
    {
        $value = null;
        if($attributeCode && $customer)
        {
            if($attributeCode == 'entity_id')
            {
                $value = $customer->getEntityId();
            }
            elseif($attributeCode == 'email')
            {
                $value = $customer->getEmail();
            }
            else
            {
                try {
                    $customerAttribute = $this->_eavAttributeRepository->get(
                        \Magento\Customer\Model\Customer::ENTITY, $attributeCode
                    );
                    if ($customerAttribute->getFrontendInput() == 'select' ||
                        $customerAttribute->getFrontendInput() == 'multiselect')
                    {
                        $value = $customerAttribute->getSource()->getOptionText($customer->getData($attributeCode));
                    }
                    elseif($customerAttribute->getBackendType() == 'datetime')
                    {
                        $value = date('Y-m-d', strtotime($customer->getData($attributeCode)));
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
                        $addressAttribute = $this->_eavAttributeRepository->get(
                            \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode
                        );
                        if ($addressAttribute->getFrontendInput() == 'select' ||
                            $addressAttribute->getFrontendInput() == 'multiselect')
                        {
                            $value =  $addressAttribute->getSource()->getOptionText($address->getData($attributeCode));
                        }
                        elseif($addressAttribute->getBackendType() == 'datetime')
                        {
                            $value = date('Y-m-d', strtotime($addressAttribute->getData($attributeCode)));
                        }
                        else
                        {
                            $value = $address->getData($attributeCode);
                        }
                    }
                }
                catch (\Exception $e)
                {

                }
            }

        }
        return $value;
    }

    /**
     * Get Product As stdClass
     *
     * @param $product
     * @return \stdClass
     */
    public function getObjProduct($product)
    {
        $objProduct = new \stdClass();
        if($product)
        {
            if($product->getImage())
            {
                $productImage = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                . 'catalog/product' . $product->getImage();
            }
            else
            {
                $productImage = $this->_imageHelper->init($product,'product_page_image_large')
                    ->keepAspectRatio(true)->getUrl();
            }
            $objProduct->id = $product->getEntityId();
            $objProduct->sku = $product->getSku();
            $objProduct->name = $product->getName();
            $objProduct->price = (float)round($product->getFinalPrice(),2);
            $objProduct->imageUrl = $productImage;
            $objProduct->linkUrl = $product->getProductUrl();
            $objProduct->shortDescription = ''.$product->getShortDescription();
            $categories = array();
            foreach($product->getCategoryIds() as $categoryId)
            {
                try
                {
                    $category = $this->_categoryRepository->get($categoryId);
                    $categories[] = $category->getName();
                }
                catch (\Magento\Framework\Exception\NoSuchEntityException $e)
                {}
            }
            $objProduct->category = $categories;
        }
        return $objProduct;
    }
}