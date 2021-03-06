<?php
namespace Contactlab\Hub\Block;

use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Model\Event\Strategy\ProductView as EventStrategyProduct;
use Contactlab\Hub\Model\Hub\Strategy\Product as HubStrategyProduct;
use Contactlab\Hub\Api\Data\EventInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Catalog\Model\Layer\Resolver as Layer;
use Magento\Framework\View\Element\Template\Context;

/**
 * Contactlab Hub Block Js
 */
class Js extends Template
{
    protected $_hubHelper;
    protected $_event;
    protected $_eventStrategyProduct;
    protected $_hubStrategyProduct;
    protected $_registry;
    protected $_categoryRepository;
    protected $_imageHelper;
    protected $_customerSession;
    protected $_layerResolver;
    protected $_currentCustomer;

    /**
     * The property is used to define content-scope of block. Can be private or public.
     * If it isn't defined then application considers it as false.
     *
     * @var bool

    protected $_isScopePrivate = true;
     */
    public function __construct(
        HubHelper $hubHelper,
        EventInterface $event,
        EventStrategyProduct $eventStrategyProduct,
        HubStrategyProduct $hubStrategyProduct,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        ImageHelper $imageHelper,
        CustomerSession $customerSession,
        Layer $layerResolver,
        Context $context,
        CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_hubHelper = $hubHelper;
        $this->_event = $event;
        $this->_eventStrategyProduct = $eventStrategyProduct;
        $this->_hubStrategyProduct = $hubStrategyProduct;
        $this->_registry = $registry;
        $this->_categoryRepository = $categoryRepository;
        $this->_imageHelper = $imageHelper;
        $this->_customerSession = $customerSession;
        $this->_layerResolver = $layerResolver;
        $this->_currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * Get block cache life time
     *
     * @return int|bool|null

    public function getCacheLifetime()
    {
        return null;
    }
     */
    /**
     * Return the current category
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * Return the current product
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getCurrentLayer()
    {
        return $this->_layerResolver->get();
    }


    /**
     * Return config api id, key and workspace to json
     * @return string
     */
    public function getConfigData()
    {
        $config = new \stdClass();
        $config->workspaceId = $this->_hubHelper->getApiWorkspaceId();
        $config->nodeId = $this->_hubHelper->getApiNodeId();
        $config->token = $this->_hubHelper->getApiToken();
        $config->context = $this->_hubHelper->getContext();
        $store = new \stdClass();
        $store->id = "".$this->_hubHelper->getStore()->getStoreId();
        $store->name = $this->_hubHelper->getStore()->getFrontendName();
        $store->country = $this->_hubHelper->getStoreDefaultCountry();
        $store->website = $this->_hubHelper->getStore()->getBaseUrl();
        $store->type = $this->_hubHelper->getContext();
        $contextInfo = new \stdClass();
        $contextInfo->store = $store;
        $config->contextInfo = $contextInfo;

        return "ch('config', ".json_encode($config).");";
    }

    /**
     * Return the Event corresponding to the page viewed
     * @return Js|string
     */
    public function getEventData()
    {
        $eventData = '';
        switch ($this->getFullActionName())
        {
            case 'catalog_category_view':
                $eventData = $this->getCategoryEvent();
                break;

            case 'catalog_product_view':
                $eventData = $this->getProductEvent();
                break;

            case 'catalogsearch_result_index':
                $eventData =  $this->getSearchEvent();
                break;
        }
        return $eventData;
    }

    /**
     * Return the Category Json Event Data
     * @return Js|string
     */
    public function getCategoryEvent()
    {
        $evt = "";
        $evtName = 'viewedProductCategory';
        if ($this->_hubHelper->isEnableEvent($evtName))
        {
            $category = $this->getCurrentCategory();
            $hubEvent = new \stdClass();
            $hubEvent->type = 'viewedProductCategory';
            $hubEvent->additionalProperties = false;
            $hubEvent->properties = new \stdClass();
            $hubEvent->properties->category = $this->_hubHelper->clearStrings($category->getName());

            $evt.= $this->_getCoustomerData();
            $evt.= "\nch('event',".json_encode($hubEvent).");";
        }
        else
        {
            $this->_hubHelper->log($evtName.' OFF');
        }
        return $evt;
    }

    /**
     * Return the Product Json Event Data
     *
     * @return string
     */
    public function getProductEvent()
    {
        $evt = "";
        $evtName = 'viewedProduct';
        if ($this->_hubHelper->isEnableEvent($evtName))
        {
            $productJs = new \stdClass();
            $product = $this->getCurrentProduct();
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
            $evt.= $this->_getCoustomerData();
            $productJs->type = 'viewedProduct';
            $properties = new \stdClass();
            $properties->id = $product->getEntityId();
            $properties->sku = $product->getSku();
            $properties->name = $this->_hubHelper->clearStrings($product->getName());
            $properties->price = round($product->getFinalPrice(),2);
            $properties->imageUrl = $productImage;
            $properties->linkUrl = $product->getProductUrl();
            if($product->getShortDescription()) {
                $properties->shortDescription = $this->_hubHelper->clearStrings($product->getShortDescription());
            }
            $categories = array();
            foreach($product->getCategoryIds() as $categoryId)
            {
                try {
                    $category = $this->_categoryRepository->get($categoryId);
                    $categories[] = $category->getName();
                }
                catch (\Magento\Framework\Exception\NoSuchEntityException $e)
                {}
            }
            $properties->category = $categories;
            $productJs->properties = $properties;
            $evt.= "\nch('event',".json_encode($productJs).");";
        }
        else
        {
            $this->_hubHelper->log($evtName.' OFF');
        }
        return $evt;
    }

    /**
     * Return the Search Json Event Data
     * @return Js|string
     */
    public function getSearchEvent(){
        $evt = "";
        $evtName = 'searched';
        if ($this->_hubHelper->isEnableEvent($evtName))
        {
            $hubEvent = new \stdClass();
            $searchQuery = $this->getRequest()->getParam('q');
            $currentLayer = $this->getCurrentLayer();
            $searchResult = ($currentLayer) ? count($currentLayer->getProductCollection()->getAllIds()) : 0;
            $hubEvent->type = 'searched';
            $hubEvent->properties = new \stdClass();
            $hubEvent->properties->keyword = $this->_hubHelper->clearStrings($searchQuery);
            $hubEvent->properties->resultCount = $searchResult;

            $evt.= $this->_getCoustomerData();
            $evt.= "\nch('event',".json_encode($hubEvent).");";
        }
        else
        {
            $this->log($evtName.' OFF');
        }
        return $evt;
    }

    /**
     * Return Customer data Json if customer is logged in
     * @return JS|string
     */
    protected function _getCoustomerData()
    {
        $return = "";
        if($this->_customerSession->isLoggedIn())
        {
            $customer = $this->_customerSession->getCustomer();
            $customerInfo = new \stdClass();
            $base = new \stdClass();
            $base->firstName = $customer->getFirstname();
            $base->lastName = $customer->getLastname();
            $contacts = new \stdClass();
            $contacts->email = $customer->getEmail();
            $base->contacts = $contacts;
            $customerInfo->base = $base;
            $return = "\nch('customer',".json_encode($customerInfo).");";
        }
        return $return;
    }

    /**
     * Retrurn if Js Tracking is Enabled
     *
     * @return bool
     */
    public function isJsTrackingEnabled()
    {
        return $this->_hubHelper->isJsTrackingEnabled($this->_hubHelper->getStore()->getStoreId());
    }
}
