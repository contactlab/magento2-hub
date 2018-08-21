<?php
/*
namespace Contactlab\Hub\Model\Adminhtml\System\Config\Backend\Map;

class Customer extends \Magento\Config\Model\Config\Backend\Serialized
{
    const XML_PATH_HUB_FIELD_ATTRIBUTE = 'customer_mapping';


    public function beforeSave()
    {

        $_value = $this->getValue();
        unset($_value[static::XML_PATH_HUB_FIELD_ATTRIBUTE][-1]);
        $startOne = array_combine(range(1, count($_value[static::XML_PATH_HUB_FIELD_ATTRIBUTE])),
            array_values($_value[static::XML_PATH_HUB_FIELD_ATTRIBUTE]));
        $_value[static::XML_PATH_HUB_FIELD_ATTRIBUTE] = $startOne;
        $this->setValue($_value);

        parent::beforeSave();
    }
}
*/


namespace Contactlab\Hub\Model\Adminhtml\System\Config\Backend\Map;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class CountryCreditCard
 */
class Customer extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if(array_key_exists('__empty', $value))
        {
            unset($value['__empty']);
        }
        $this->setValue($this->serializer->serialize($value));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            if (is_array($value)) {
                $this->setValue($value);
            }
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $hubType => $creditCardType) {
            $id = $this->mathRandom->getUniqueHash('_');
            $result[$id] = ['hub_type' => $hubType, 'magento_attribute' => $creditCardType];
        }
        return $result;
    }
}
