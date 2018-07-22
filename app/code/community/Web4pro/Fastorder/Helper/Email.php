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
class Web4pro_Fastorder_Helper_Email extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ADMIN_EMAIL_TEMPLATE = 'web4pro_fastorder/general/template_admin';
    const XML_PATH_EMAIL_FAST_ORDER_CUSTOMER_TEMPLATE = 'web4pro_fastorder/general/template_customer';

    /**
     * @param Web4pro_Fastorder_Model_Order $order
     * @return Web4pro_Fastorder_Helper_Email
     */
    public function sendOrderEmailToAdmin($order)
    {
        $helper = Mage::helper('web4pro_fastorder');
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $magentoOrder = Mage::getModel('sales/order')->load($order->getMagentoOrderId());

        $mailTemplate = Mage::getModel('core/email_template');

        $template = Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_TEMPLATE);
        $recipientEmail = $helper->getOrderNotificationEmail();
        $recipientName = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
        $customer = $order->getCustomer();

        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->sendTransactional(
                $template,
                'general',
                $recipientEmail,
                $recipientName,
                array(
                    'customer' => $customer,
                    'order' => $order,
                    'invoice' => $order->getQuote(),
                    'increment_id' => $magentoOrder->getIncrementId(),
                )
            );

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * @param $order
     * @return $this
     */

    public function sendOrderEmailToCustomer($order)
    {
        $helper = Mage::helper('web4pro_fastorder');
        $customer = $this->getCustomerDataFromFastOrder($order);
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getName();
        if((empty($customerEmail) || !$helper->isSendEmailToCustomer()) && !$helper->registerGuestAsCustomer()){
            return false;
        }
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');
        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_FAST_ORDER_CUSTOMER_TEMPLATE );
        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->sendTransactional(
                $templateId,
                'general',
                $customerEmail,
                $customerName,
                array(
                    'customer' => $customer,
                    'order' => $order,
                    'invoice' => $order->getQuote(),
                    'fast_order' => $customer->getFastOrderInstance(),
                )
            );
        $translate->setTranslateInline(true);
        return true;
    }

    public function getCustomerDataFromFastOrder($order)
    {
        $magentoOrderId = $order->getId();
        $fastOrderInstance = Mage::getModel('web4pro_fastorder/order')->getCollection()
            ->addFieldToFilter('magento_order_id', array('eq' => $magentoOrderId))->getFirstItem();
        $customerId = $fastOrderInstance->getCustomerId();
        if(!$customerId){
            $customer = $this->_prepareDefaultCustomerData();
        }
        else {
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }
        $customer->setEmail($fastOrderInstance->getCustomerEmail());
        $customer->setPhone($fastOrderInstance->getPhone());
        $customer->setFastOrderInstance($fastOrderInstance);
        return $customer;
    }

    protected function _prepareDefaultCustomerData()
    {
        $customer = Mage::getModel('customer/customer');
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        $customer->setWebsiteId($websiteId);
        $customer->setStore($store);
        $customer->setFirstname('Guest');
        $customer->setLastname('Guest');
        return $customer;
    }

}