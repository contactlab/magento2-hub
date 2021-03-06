<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CartAddProduct;
use Contactlab\Hub\Model\Event\Strategy\CartRemoveProduct;
use Contactlab\Hub\Helper\Data as HubHelper;

class CheckoutCartUpdateItemsBefore implements ObserverInterface
{
    protected $_customerSession;
    protected $_eventService;
    protected $_addedStrategy;
    protected $_removedStrategy;
    protected $_productRepository;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        CartAddProduct $addedStrategy,
        CartRemoveProduct $removedStrategy,
        ProductRepositoryInterface $productRepository,
        HubHelper $helper
    )
    {
        $this->_customerSession = $customerSession;
        $this->_eventService = $eventService;
        $this->_addedStrategy = $addedStrategy;
        $this->_removedStrategy = $removedStrategy;
        $this->_productRepository = $productRepository;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        if($this->_eventService->getSid())
        {
            $info = $observer->getInfo()->getData();
            $quote =  $observer->getCart()->getQuote();
            foreach($quote->getAllVisibleItems() as $item)
            {
                if(array_key_exists($item->getItemId(), $info))
                {
                    $newQty = $info[$item->getItemId()]['qty'];
                    if ($newQty > $item->getQty()) {
                        $qty = $newQty - $item->getQty();
                        $strategy = $this->_addedStrategy;
                    } else {
                        $qty = $item->getQty() - $newQty;
                        $strategy = $this->_removedStrategy;
                    }
                    $product = $this->_productRepository->get($item->getSku());
                    $objProduct = $this->_helper->getObjProduct($product);
                    $objProduct->quantity = $qty;
                    $data['product'] = $objProduct;
                    $data['store_id'] = $product->getStoreId();
                    $data['email'] = $this->_customerSession->getCustomer()->getEmail();
                }
            }
            $strategy->setContext($data);
            $this->_eventService->collectEvent($strategy);
        }
    }
}