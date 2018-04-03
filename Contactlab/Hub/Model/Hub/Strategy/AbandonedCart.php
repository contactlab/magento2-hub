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
        //$hubEvent->properties->abandonedCartUrl = '';
        $hubEvent->properties->amount = new \stdClass();
        $hubEvent->properties->amount->total = (float)$quote->getGrandTotal();
        //$hubEvent->properties->amount->revenue = (float)($quote->getGrandTotal() - $quote->getShippingAmount() - $quote->getShippingTaxAmount());
        //$hubEvent->properties->amount->shipping = (float)($quote->getShippingAmount() + $quote->getShippingTaxAmount());
        $hubEvent->properties->amount->tax = (float)$quote->getTaxAmount();
        //$hubEvent->properties->amount->discount = (float)$quote->getDiscountAmount();
        $hubEvent->properties->amount->local = new \stdClass();
        $hubEvent->properties->amount->local->currency = $quote->getQuoteCurrencyCode();
        $hubEvent->properties->amount->local->exchangeRate = (float)$quote->getStoreToQuoteRate();
        $arrayProducts = array();
        foreach($quote->getAllItems() as $item)
        {
            if(!$item->getParentItemId())
            {
                $price = (float)$item->getPriceInclTax();
                $tax = (float)$item->getTaxAmount();
                $discount = abs((float)$item->getDiscountAmount());
                $qty = (int)$item->getQty();
                $subtotal = $item->getRowTotalInclTax() - $item->getDiscountAmount();

                $product = $this->_productRepository->getById(
                    $item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_helper->getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->price = $price;
                $objProduct->tax = $tax;
                $objProduct->discount = $discount;
                $objProduct->quantity = $qty;
                $objProduct->subtotal = $subtotal;
                if($quote->getCouponCode())
                {
                    $objProduct->coupon = $quote->getCouponCode();
                }
                $arrayProducts[] = $objProduct;
            }
        }
        $hubEvent->properties->products = $arrayProducts;

        return $hubEvent;
    }
}
