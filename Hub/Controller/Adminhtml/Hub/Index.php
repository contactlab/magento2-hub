<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 28/06/17
 * Time: 10:00
 */
namespace Contactlab\Hub\Controller\Adminhtml\Hub;

use Contactlab\Hub\Controller\Adminhtml\Hub as HubController;

class Index extends HubController
{
    const ACTIVE_MENU = 'Contactlab_Core::core';
    /**
     * Events list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ACTIVE_MENU);
        $resultPage->getConfig()->getTitle()->prepend(__('Contactlab Hub'));
        $resultPage->addBreadcrumb(__('Contactlab'), __('Contactlab'));
        $resultPage->addBreadcrumb(__('Hub'), __('Event List'));
        return $resultPage;
    }
}
