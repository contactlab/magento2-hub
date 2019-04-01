<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 07/01/2019
 * Time: 14:49
 */

namespace Contactlab\Hub\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Module\ResourceInterface as ModuleResource;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\ProductMetadata;
use Contactlab\Hub\Model\Service\Hub as HubService;
use Contactlab\Hub\Helper\Data as HubHelper;

class Checks extends Template
{

    const STATUS_KO = -1;
    const STATUS_IMPROVBLE = 0;
    const STATUS_OK = 3;

    const PHP_MIN_VERSION = '7.0';
    const MAGENTO_MIN_VERSION = '2.1.17';
    const HUB_LATEST_VERSION_FILE =  'https://raw.githubusercontent.com/contactlab/magento2-hub/master/etc/module.xml';

    protected $_moduleResource;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected $_cronSchedules;

    /**
     * @var ScheduleFactory
     */
    protected $_scheduleFactory;

    protected $_date;

    protected $_productMetadata;

    protected $_hubService;

    protected $_helper;

    public function __construct(
        ModuleResource $moduleResource,
        ScheduleFactory $scheduleFactory,
        DateTime $date,
        ProductMetadata $productMetadata,
        HubService $hubService,
        HubHelper $helper,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_moduleResource = $moduleResource;
        $this->_scheduleFactory = $scheduleFactory;
        $this->_date = $date;
        $this->_productMetadata = $productMetadata;
        $this->_hubService = $hubService;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function checkPhpVersion()
    {
        $check = [
            'code' => self::STATUS_KO,
            'message' => __(
                'Your PHP version is not compatible with this module. Please update PHP to %1 version',
                self::PHP_MIN_VERSION
            )
        ];
        if(version_compare(phpversion(), self::PHP_MIN_VERSION) >= 0)
        {
            $check = ['code' => self::STATUS_OK, 'message' => __('Your PHP version is compatible with this module')];
        }
        return $check;
    }

    public function checkMagentoVersion()
    {

        $check = [
            'code' => self::STATUS_KO,
            'message' => __(
                'Your Magento version is not compatible with this module. Please update Magento to %1 version',
                self::MAGENTO_MIN_VERSION
            )
        ];
        if(version_compare($this->_productMetadata->getVersion(), self::MAGENTO_MIN_VERSION) >= 0)
        {
            $check = ['code' => self::STATUS_OK, 'message' => __('Your Magento version is compatible with this module')];
        }
        return $check;
    }

    public function getModuleVersion()
    {
        return $this->_moduleResource->getDbVersion('Contactlab_Hub');
    }

    public function checkModuleVersion()
    {
        $check = [
            'code' => self::STATUS_IMPROVBLE,
            'message' => __(
                'We can\'t automatically check if Contactlab_Hub is installed at the latest version. 
                Please check your version on github'
            )
        ];
        if($xml = simplexml_load_file(self::HUB_LATEST_VERSION_FILE))
        {
            if (version_compare($this->getModuleVersion(), $xml->module['setup_version']) >= 0) {
                $check = ['code' => self::STATUS_OK, 'message' => __('Contactlab_Hub is installed at the latest version')];
            }
            else
            {
                $check = [
                    'code' => self::STATUS_KO,
                    'message' => __(
                        'Contactlab_Hub version is not installed at the latest version. Please update to %1 version',
                        $xml->module['setup_version']
                    )
                ];
            }
        }
        return $check;
    }

    public function checkCron()
    {
        $check = ['code' => self::STATUS_KO, 'message' => __('Magento Cron is not running')];
        $datetime1 = $this->_date->date();
        foreach ($this->_getCronSchedules() as $cronSchedule)
        {
            $datetime2 = $this->_date->date($cronSchedule->getCreatedAt());
            $interval = round((strtotime($datetime1) - strtotime($datetime2)) / 60);
            if($interval > 5)
            {
                $intervalTime = $interval;
                $time = 'minutes';
                if($interval > 120) //hours
                {
                    $intervalTime = round($interval / 60);
                    $time = 'hours';
                }
                if($interval > 2880) //days
                {
                    $intervalTime = round($interval / 1440);
                    $time = 'days';
                }
                $check = [
                    'code' => self::STATUS_IMPROVBLE,
                    'message' => __('Magento Cron is not running until %1 %2', $intervalTime, $time)
                ];
            }
            else
            {
                $check = ['code' => self::STATUS_OK,'message' => __('Magento Cron runs properly')];
            }
        }

        return $check;
    }

    /**
     * Return job collection from data base with status 'pending'
     *
     * @return \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected function _getCronSchedules()
    {
        if (!$this->_cronSchedules)
        {
            $this->_cronSchedules = $this->_scheduleFactory->create()->getCollection()
                ->setPageSize(1)
                ->setCurPage(1)
                ->setOrder('created_at','DESC')
                ->load();
        }
        return $this->_cronSchedules;
    }


    public function checkApi()
    {
        $check = ['code' => self::STATUS_KO, 'message' => __('API not properly configured')];
        $apiToken = $this->_helper->getApiToken();
        $apiWorkspace = $this->_helper->getApiWorkspaceId();
        $apiNodeId = $this->_helper->getApiNodeId();
        $apiUrl = $this->_helper->getApiUrl();
        if(($apiToken) && ($apiWorkspace) && ($apiNodeId) && ($apiUrl))
        {
            $response = $this->_hubService->getCustomers();
            if($response->curl_http_code != '200')
            {
                $check = ['code' => self::STATUS_IMPROVBLE, 'message' => __('There was an error with your API configuration')];
            }
            else
            {
                $check = ['code' => self::STATUS_OK, 'message' => __('API works properly')];
            }
        }
        return $check;
    }
}
