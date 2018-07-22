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
class Web4pro_Fastorder_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'web4pro_fastorder';
        $this->_controller = 'adminhtml_orders';
        $this->_headerText = Mage::helper('web4pro_fastorder')->__('Manage Fast Orders');
        parent::__construct();
        $this->_removeButton('add');
    }

}