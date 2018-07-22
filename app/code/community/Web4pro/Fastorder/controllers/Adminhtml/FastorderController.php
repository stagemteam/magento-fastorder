<?php
/**
 * WEB4PRO - Creating profitable online stores
 *
 *
 * @author    WEB4PRO <amutylo@web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_Fastorder
 * @copyright Copyright (c) 2014 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */
class Web4pro_Fastorder_Adminhtml_FastorderController extends Mage_Adminhtml_Controller_Action
{

    protected function _isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/fast_order/' . $action);
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('adminhtml/session');
        $this->_setActiveMenu('sales/fastorder');
        $this->renderLayout();
    }

    /**
     * grid
     */
    public function gridAction()
    {
        $block = $this->getLayout()->createBlock('web4pro_fastorder/adminhtml_orders_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * grid
     */
    public function cartgridAction()
    {
        $order = Mage::getModel('web4pro_fastorder/order')->load(
            $this->getRequest()->getParam('id')
        );
        Mage::register('order', $order);
        $block = $this->getLayout()->createBlock('web4pro_fastorder/adminhtml_orders_view_tab_cart');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function viewAction()
    {
        $order = Mage::getModel('web4pro_fastorder/order')->load(
            $this->getRequest()->getParam('id')
        );
        Mage::register('order', $order);
        if (!$order->getId()) {
            return $this->_redirect('*/*/index');
        }
        $this->loadLayout();
        $this->_initLayoutMessages('adminhtml/session');
        $this->_setActiveMenu('sales/fastorder');

        $this->renderLayout();
    }

    /**
     * check permissions
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'grid':
            case 'cartgrid':
                return $this->_isActionAllowed('grid');
                break;
            case 'view':
                return $this->_isActionAllowed('view');
                break;
            case 'delete':
                return $this->_isActionAllowed('delete');
                break;
        }
        return false;
    }
}