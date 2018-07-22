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
class Web4pro_Fastorder_Block_Adminhtml_Orders_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'view';
        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('sales_order_view');

    }

    /**
     * Retrieve order model object
     *
     * @return Web4pro_Fastorder_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('order');
    }

    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }


    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Order # %s | %s', $this->getOrder()->getEntityId(), $this->formatDate($this->getOrder()->getCreateDate(), 'medium', true));
    }

}
