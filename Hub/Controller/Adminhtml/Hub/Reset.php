<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 29/06/17
 * Time: 15:54
 */

namespace Contactlab\Hub\Controller\Adminhtml\Hub;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Contactlab\Hub\Api\EventRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Contactlab\Hub\Controller\Adminhtml\Hub as HubController;
use Contactlab\Hub\Api\PreviousCustomerManagementInterface;

class Reset extends HubController
{

    protected $_previousCustomerService;
    protected $_jsonFactory;

    /**
     * constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param EventRepositoryInterface $eventRepository
     * @param PageFactory $resultPageFactory
     * @param PreviousCustomerManagementInterface $prviousCustomerService
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EventRepositoryInterface $eventRepository,
        PageFactory $resultPageFactory,
        PreviousCustomerManagementInterface $prviousCustomerService,
        JsonFactory $jsonFactory
    )
    {
        $this->_jsonFactory = $jsonFactory;
        $this->_previousCustomerService = $prviousCustomerService;
        parent::__construct( $context, $coreRegistry, $eventRepository, $resultPageFactory);
    }

    /**
     * Reset Previous Customers.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_jsonFactory->create();
        try {
            $this->_previousCustomerService->resetPreviousCustomers()
                ->collectPreviousCustomers();
            $result->setData(['success' => true]);
        } catch (\Exception $e) {
            $result->setData(['error' => $e]);
        }
        return $result;
    }



}
