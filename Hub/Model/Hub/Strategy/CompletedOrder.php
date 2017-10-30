<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 11:47
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy\Product as StrategyProduct;
use Contactlab\Hub\Helper\Data as HubHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CompletedOrder extends StrategyProduct
{
    protected $_order;
    protected $_helper;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        CategoryRepositoryInterface $categoryRepository,
        OrderInterface $order,
        HubHelper $helper

    ){
        $this->_order = $order;
        $this->_helper = $helper;
        parent::__construct($productRepository, $imageHelper, $categoryRepository);
    }

    /**
     * Build
     *
     * @return \stdClass
     */
    public function build()
    {
        $hubEvent = new \stdClass();
        $hubEvent->properties = new \stdClass();
        $eventData = json_decode($this->_event->getEventData());
        $hubEvent->properties->type = $eventData->type;
        $order = $this->_order->loadByIncrementId($eventData->increment_id);
        //$order = $this->_order->loadByIncrementIdAndStoreId($eventData->increment_id, $this->_event->getStoreId());
        $hubEvent->properties->orderId = strval($order->getIncrementId());
        $hubEvent->properties->storeCode = "".$order->getStoreId();
        /* TODO paymentMethod -> "cash","creditcard","debitcard","paypal","other" da definire in backoffice con un match
        $hubEvent->properties->paymentMethod = 'cash';
        */
        $hubEvent->properties->amount = new \stdClass();
        $exchangeRate = (float)$order->getStoreToOrderRate();
        if($exchangeRate == 0)
        {
            $exchangeRate = $this->_helper->getExchangeRate($order->getStoreId());
        }

        $hubEvent->properties->amount->total = $this->_helper->convertToBaseRate($order->getGrandTotal(), $exchangeRate);
        $hubEvent->properties->amount->revenue = $this->_helper->convertToBaseRate(($order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount()), $exchangeRate);
        $hubEvent->properties->amount->shipping = $this->_helper->convertToBaseRate(($order->getShippingAmount() + $order->getShippingTaxAmount()), $exchangeRate);
        $hubEvent->properties->amount->tax = $this->_helper->convertToBaseRate($order->getTaxAmount(), $exchangeRate);
        $hubEvent->properties->amount->discount = $this->_helper->convertToBaseRate($order->getDiscountAmount(), $exchangeRate);
        $hubEvent->properties->amount->local = new \stdClass();
        $hubEvent->properties->amount->local->currency = $order->getOrderCurrencyCode();
        $hubEvent->properties->amount->local->exchangeRate = $exchangeRate;
        $arrayProducts = array();
        foreach($order->getAllItems() as $item)
        {
            if (!$item->getParentItemId())
            {
                $product = $this->_productRepository->getById($item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->price = $this->_helper->convertToBaseRate($item->getPrice(), $exchangeRate);
                $objProduct->subtotal = $this->_helper->convertToBaseRate($item->getRowTotal(), $exchangeRate);
                $objProduct->quantity = (int)$item->getQtyOrdered();
                $objProduct->discount = $this->_helper->convertToBaseRate($item->getDiscountAmount(), $exchangeRate);
                $objProduct->tax = $this->_helper->convertToBaseRate($item->getTaxAmount(), $exchangeRate);
                if($order->getCouponCode())
                {
                    $objProduct->coupon = $order->getCouponCode();
                }
                $arrayProducts[] = $objProduct;
            }
        }
        $hubEvent->properties->products = $arrayProducts;
        return $hubEvent;
    }
}