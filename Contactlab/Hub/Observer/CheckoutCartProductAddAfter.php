<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CartAddProduct;

class CheckoutCartProductAddAfter  implements ObserverInterface
{
    protected $_customerSession;
    protected $_eventService;
    protected $_strategy;
    protected $_productRepository;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        CartAddProduct $strategy,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->_customerSession = $customerSession;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        $item =  $observer->getQuoteItem();
        $product = $this->_productRepository->get($item->getSku());
        $data = $item->getData() + $product->getData();
        $data['email'] = $this->_customerSession->getCustomer()->getEmail();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}