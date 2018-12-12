<?php
namespace Contactlab\Hub\Model\Config\Cron;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Contactlab\Hub\Model\Config\Source\Cron\Frequency;

class Events extends \Magento\Framework\App\Config\Value
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/contactlab_hub/jobs/contactlab_hub_export_event/schedule/cron_expr';


    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/contactlab_hub/jobs/contactlab_hub_export_event/run/model';


    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ValueFactory $configValueFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

        /**
         * {@inheritdoc}
         *
         * @return $this
         * @throws \Exception
         */
        public function afterSave()
    {

        $frequency = $this->getData('groups/cron_events/fields/frequency/value');
        $time = $this->getData('groups/cron_events/fields/time/value');
        $repeatMinutes = $this->getData('groups/cron_events/fields/repeat_minutes/value');
        $repeatHours = $this->getData('groups/cron_events/fields/repeat_hours/value');

        $frequencyMinutes = Frequency::CRON_MINUTES;
        $frequencyHourly = Frequency::CRON_HOURLY;
        //$frequencyDaily = Frequency::CRON_DAILY;
        $frequencyWeekly = Frequency::CRON_WEEKLY;
        $frequencyMonthly = Frequency::CRON_MONTHLY;


        if($frequency == $frequencyMinutes)
        {
            $cronExprArray = [
                ($repeatMinutes > 1) ? '*/'.$repeatMinutes : '*', 	# Minute
                '*',         				                        # Hour
                '*',     											# Day of the Month
                '*',                                                # Month of the Year
                '*',       											# Day of the Week
            ];
        }
        elseif($frequency == $frequencyHourly)
        {
            $cronExprArray = [
                intval($time[1]),  									# Minute
                ($repeatHours > 1) ? '*/'.$repeatHours : '*',		# Hour
                '*',     											# Day of the Month
                '*',                                                # Month of the Year
                '*',       											# Day of the Week
            ];
        }
        else
        {
            $cronExprArray = [
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
                '*',                                                # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
            ];
        }

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save the cron expression.'));
        }

        return parent::afterSave();
    }
}