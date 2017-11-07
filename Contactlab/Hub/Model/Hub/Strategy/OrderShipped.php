<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 20/06/17
 * Time: 16:06
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy\Product as StrategyProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Api\Data\ShipmentInterface;


class OrderShipped extends StrategyProduct
{
    protected $_shipment;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        CategoryRepositoryInterface $categoryRepository,
        ShipmentInterface $shipment
    ){
        parent::__construct($productRepository, $imageHelper, $categoryRepository);
        $this->_shipment = $shipment;
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
        $shipment = $this->_shipment->loadByIncrementId($eventData->shipment_id);
        $order = $shipment->getOrder();
        $hubEvent->properties->orderId = strval($order->getIncrementId());
        foreach($shipment->getAllTracks() as $track)
        {
            if($track->getTitle())
            {
                $hubEvent->properties->carrier = $track->getTitle();
            }
            if($track->getTrackNumber())
            {
                $hubEvent->properties->trackingCode = $track->getTrackNumber();
            }
            if($track->getWeight())
            {
                $hubEvent->properties->weight = $track->getWeight();
            }
        }
        if($shipment->getPackages())
        {
            $hubEvent->properties->packages = $shipment->getPackages();
        }
        $arrayProducts = array();
        foreach($shipment->getItemsCollection() as $item)
        {
            if (!$item->getParentItemId())
            {
                $product = $this->_productRepository->getById($item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_getObjProduct($product);
                $objProduct->type = $eventData->type;
                $objProduct->quantity = (int)$item->getQty();
                $objProduct->weight = (float)$item->getWeight();
                $arrayProducts[] = $objProduct;
            }
        }
        $hubEvent->properties->extraProperties = new \stdClass();
        $hubEvent->properties->extraProperties->products = $arrayProducts;
        return $hubEvent;
    }
}
