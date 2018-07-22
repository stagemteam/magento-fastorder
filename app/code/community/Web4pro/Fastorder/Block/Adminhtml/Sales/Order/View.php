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
class Web4pro_Fastorder_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->helper('web4pro_fastorder')->getPhoneNumberByOrder($this->getOrder()->getId());
    }

    protected function _toHtml()
    {
        if ($this->helper('web4pro_fastorder')->isDisplayPhoneInSalesOrders() && $this->getPhoneNumber()) {
            return parent::_toHtml();
        }
        return '';
    }
}