<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 22/06/17
 * Time: 11:50
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy\Product as StrategyProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;

class AbandonedCart extends StrategyProduct
{
    protected $_quoteFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        CategoryRepositoryInterface $categoryRepository,
        QuoteFactory $quoteFactory
    )
    {
        parent::__construct($productRepository, $imageHelper, $categoryRepository);
        $this->_quoteFactory = $quoteFactory;
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
                $product = $this->_productRepository->getById($item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->price = (float)$item->getPrice();
                $objProduct->subtotal = (float)$item->getRowTotal();
                $objProduct->quantity = (int)$item->getQty();
                $objProduct->discount = (float)$item->getDiscountAmount();
                $objProduct->tax = (float)$item->getTaxAmount();
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
