<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CompareRemoveProduct;
use Contactlab\Hub\Helper\Data as HubHelper;

class CatalogProductCompareRemoveProduct implements ObserverInterface
{
    protected $_customerSession;
    protected $_productRepository;
    protected $_eventService;
    protected $_strategy;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        EventManagementInterface $eventService,
        CompareRemoveProduct $strategy,
        HubHelper $helper
    )
    {
        $this->_customerSession = $customerSession;
        $this->_productRepository = $productRepository;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $product = $this->_productRepository->getById($observer->getProduct()->getProductId());
        $data['product'] = $this->_helper->getObjProduct($product);
        $data['store_id'] = $product->getStoreId();
        $data['email'] = $this->_customerSession->getCustomer()->getEmail();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}