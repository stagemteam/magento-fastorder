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
class Web4pro_Fastorder_Block_Success extends Web4pro_Fastorder_Block_Abstract
{

    /**
     * @return null|string
     */
    public function getOrderId()
    {
        $id = $this->_getData('order_id');
        return $id ? $id : null;
    }

    /**
     * @return null|string
     */
    public function getPhoneNumber()
    {
        return $this->_getData('phone');
    }

    public function getCustomerEmail()
    {
        return $this->_getData('customer_email');
    }

    /**
     * @return bool
     */
    public function isShowMagentoOrderSuccess()
    {
        if (Mage::helper('web4pro_fastorder')->isSaveMagentoOrder() && $this->_getData('magento_order_id')) {
            return true;
        }
        return false;
    }

    protected function _prepareData()
    {
        $orderId = Mage::getSingleton('checkout/session')->getFastOrderId();

        if ($orderId) {
            $order = Mage::getModel('web4pro_fastorder/order')->load($orderId);

            $magOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $magOrder = Mage::getModel('sales/order')->load($magOrderId);
            $billing = $magOrder->getBillingAddress();
            $address = Mage::getModel('sales/order_address')->load($billing->getId());

            if ($order->getId()) {
                $this->addData(array(
                    'order_id' => $orderId,
                    'phone' => $order->getFullPhoneNumber(),
                    'magento_order_id' => $order->getMagentoOrderId(),
                    'customer_email' => $address->getEmail()
                ));
            }
        }

    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        parent::_beforeToHtml();
    }
}