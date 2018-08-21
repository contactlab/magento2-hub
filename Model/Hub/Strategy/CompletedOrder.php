<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 11:47
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Contactlab\Hub\Model\Hub\Strategy;
use Contactlab\Hub\Helper\Data as HubHelper;

class CompletedOrder extends Strategy
{
    protected $_order;
    protected $_productRepository;
    protected $_helper;

    public function __construct(
        OrderInterface $order,
        ProductRepositoryInterface $productRepository,
        HubHelper $helper

    ){
        $this->_order = $order;
        $this->_productRepository = $productRepository;
        $this->_helper = $helper;
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
        $total = $order->getGrandTotal();
        $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
        $tax = $order->getTaxAmount() -  $order->getShippingTaxAmount();
        $discount = abs($order->getDiscountAmount());
        $revenue = $total - $shipping - $discount;
        $hubEvent->properties->amount->total = $this->_helper->convertToBaseRate($total, $exchangeRate);
        $hubEvent->properties->amount->shipping = $this->_helper->convertToBaseRate($shipping, $exchangeRate);
        $hubEvent->properties->amount->tax = $this->_helper->convertToBaseRate($tax, $exchangeRate);
        $hubEvent->properties->amount->discount = $this->_helper->convertToBaseRate($discount, $exchangeRate);
        $hubEvent->properties->amount->revenue = $this->_helper->convertToBaseRate($revenue, $exchangeRate);
        $hubEvent->properties->amount->local = new \stdClass();
        $hubEvent->properties->amount->local->currency = $order->getOrderCurrencyCode();
        $hubEvent->properties->amount->local->exchangeRate = $exchangeRate;
        $arrayProducts = array();
        foreach($order->getAllItems() as $item)
        {
            if (!$item->getParentItemId())
            {
                $price = $this->_helper->convertToBaseRate($item->getPriceInclTax(), $exchangeRate);
                $tax = $this->_helper->convertToBaseRate($item->getTaxAmount(), $exchangeRate);
                $discount = abs($this->_helper->convertToBaseRate($item->getDiscountAmount(), $exchangeRate));
                $qty = (int)$item->getQtyOrdered();
                $subtotal = $this->_helper->convertToBaseRate(($item->getRowTotalInclTax() - $item->getDiscountAmount()), $exchangeRate);

                $product = $this->_productRepository->getById(
                    $item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_helper->getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->price = $price;
                $objProduct->tax = $tax;
                $objProduct->discount = $discount;
                $objProduct->quantity = $qty;
                $objProduct->subtotal = $subtotal;
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