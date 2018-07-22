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

class Web4pro_Fastorder_Model_OrderStandard extends Mage_Sales_Model_Order
{
    /**
     * @return Web4pro_Fastorder_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('web4pro_fastorder');
    }

    /**
     * @return Web4pro_Fastorder_Helper_Email
     */
    protected function _getEmailHelper()
    {
        return Mage::helper('web4pro_fastorder/email');
    }

    /**
     * for Magento versions above 1.9
     * @param bool $forceMode
     * @return $this|Mage_Sales_Model_Order
     */
    public function queueNewOrderEmail($forceMode = false)
    {
        $isFastOrder = $this->_getIsFastOrder();
        if(!$isFastOrder) {
            return parent::queueNewOrderEmail($forceMode);
        }
        return $this->sendFastOrderEmail();
    }

    /**
     * For magento versions below 1.9
     * @return $this|Mage_Sales_Model_Order
     */

    public function sendNewOrderEmail()
    {
        $isFastOrder = $this->_getIsFastOrder();
        if($isFastOrder) {
            return $this->sendFastOrderEmail();
        }
        return parent::sendNewOrderEmail();
    }

    /**
     * @return $this
     */

    public function sendFastOrderEmail()
    {
        $this->_getEmailHelper()->sendOrderEmailToCustomer($this);
        return $this;
    }

    /**
     * @return bool
     */

    protected function _getIsFastOrder()
    {
        $isFastOrder = Mage::registry('save_magento_order_from_fastorder');
        $canSentFastOrderEmailToCustomemr = $this->_getHelper()->isSendEmailToCustomer();
        $res = ($isFastOrder && $canSentFastOrderEmailToCustomemr);
        return $res;
    }
}