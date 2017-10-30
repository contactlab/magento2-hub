<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 30/06/17
 * Time: 10:22
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Product extends HubStrategy
{
    protected $_productRepository;
    protected $_imageHelper;
    protected $_categoryRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        CategoryRepositoryInterface $categoryRepository
    ){
        $this->_productRepository = $productRepository;
        $this->_imageHelper = $imageHelper;
        $this->_categoryRepository = $categoryRepository;
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
        $product = $this->_productRepository->getById($eventData->product_id, false, $this->_event->getStoreId());
        $hubEvent->properties = $this->_getObjProduct($product);
        return $hubEvent;
    }


    /**
     * Get Product As stdClass
     *
     * @param $product
     * @return \stdClass
     */
    protected function _getObjProduct($product)
    {
        $objProduct = new \stdClass();
        if($product)
        {
            $objProduct->id = $product->getEntityId();
            $objProduct->sku = $product->getSku();
            $objProduct->name = $product->getName();
            $objProduct->price = (float)round($product->getFinalPrice(),2);
            $objProduct->imageUrl = $this->_imageHelper->init($product,'product_page_image_large')->keepAspectRatio(true)->getUrl();
            $objProduct->linkUrl = $product->getProductUrl();
            $objProduct->shortDescription = ''.$product->getShortDescription();
            $categories = array();
            foreach($product->getCategoryIds() as $categoryId)
            {
                $category = $this->_categoryRepository->get($categoryId);
                $categories[] = $category->getName();
            }
            $objProduct->category = $categories;
        }
        return $objProduct;
    }

}