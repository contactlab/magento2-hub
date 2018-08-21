<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 20/06/17
 * Time: 16:06
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Contactlab\Hub\Model\Hub\Strategy;
use Contactlab\Hub\Helper\Data as HubHelper;

class OrderShipped extends Strategy
{
    protected $_shipment;
    protected $_productRepository;
    protected $_helper;

    public function __construct(
        ShipmentInterface $shipment,
        ProductRepositoryInterface $productRepository,
        HubHelper $helper
    ){
        $this->_shipment = $shipment;
        $this->_productRepository= $productRepository;
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
                $product = $this->_productRepository->getById(
                    $item->getProductId(), false, $this->_event->getStoreId());
                $objProduct = $this->_helper->getObjProduct($product);
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
