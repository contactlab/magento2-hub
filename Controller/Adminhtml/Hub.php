<?php

/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 28/06/17
 * Time: 11:27
 */

namespace Contactlab\Hub\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Contactlab\Hub\Api\EventRepositoryInterface;

abstract class Hub extends Action
{
    protected $_publicActions = ['new'];
    const ADMIN_RESOURCE = 'contactlab_hub_hub';
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Event repository
     *
     * @var EventRepositoryInterface
     */
    protected $_eventRepository;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param EventRepositoryInterface $eventRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EventRepositoryInterface $eventRepository,
        PageFactory $resultPageFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_eventRepository = $eventRepository;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }



}
