<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 22/06/17
 * Time: 11:50
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Magento\Quote\Model\QuoteFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Contactlab\Hub\Model\Hub\Strategy;
use Contactlab\Hub\Helper\Data as HubHelper;

class AbandonedCart extends Strategy
{
    protected $_quoteFactory;
    protected $_productRepository;
    protected $_helper;

    public function __construct(
        QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
        HubHelper $helper
    )
    {
        $this->_quoteFactory = $quoteFactory;
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
        $quote = $this->_quoteFactory->create()->loadByIdWithoutStore($eventData->quote_id);
        $hubEvent->properties->orderId = strval($quote->getEntityId());
        $hubEvent->properties->storeCode = "" . $quote->getStoreId();
        $exchangeRate = (float)$quote->getStoreToOrderRate();
        if($exchangeRate == 0)
        {
            $exchangeRate = $this->_helper->getExchangeRate($quote->getStoreId());
        }
        $hubEvent->properties->amount = new \stdClass();
        $hubEvent->properties->amount->local = new \stdClass();
        $hubEvent->properties->amount->local->currency = $quote->getQuoteCurrencyCode();
        $hubEvent->properties->amount->local->exchangeRate = $exchangeRate;
        $arrayProducts = array();
        $totTax = 0;
        $totDiscount = 0;
        foreach($quote->getAllItems() as $item)
        {
            if(!$item->getParentItemId())
            {
                $price = (float)$item->getPriceInclTax();
                $tax = (float)$item->getTaxAmount();
                $totTax+= $tax;
                $discount = abs((float)$item->getDiscountAmount());
                $totDiscount+= $discount;
                $qty = (int)$item->getQty();
                $subtotal = $item->getRowTotalInclTax() - $item->getDiscountAmount();

                $product = $this->_productRepository->getById(
                    $item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_helper->getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->price = $this->_helper->convertToBaseRate($price, $exchangeRate);
                $objProduct->tax = $this->_helper->convertToBaseRate($tax, $exchangeRate);
                $objProduct->discount = $this->_helper->convertToBaseRate($discount, $exchangeRate);
                $objProduct->quantity = $qty;
                $objProduct->subtotal = $this->_helper->convertToBaseRate($subtotal, $exchangeRate);
                if($quote->getCouponCode())
                {
                    $objProduct->coupon = $quote->getCouponCode();
                }
                $arrayProducts[] = $objProduct;
            }
        }
        $hubEvent->properties->amount->total = $this->_helper->convertToBaseRate($quote->getGrandTotal(), $exchangeRate);
        $hubEvent->properties->amount->tax = $this->_helper->convertToBaseRate($totTax, $exchangeRate);
        $hubEvent->properties->amount->discount = $this->_helper->convertToBaseRate($totDiscount, $exchangeRate);
        $hubEvent->properties->products = $arrayProducts;

        return $hubEvent;
    }
}
