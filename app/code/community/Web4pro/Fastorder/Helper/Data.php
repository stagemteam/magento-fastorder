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
class Web4pro_Fastorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'web4pro_fastorder/general/active';
    const XML_PATH_CREATE_MAGENTO_ORDER = 'web4pro_fastorder/general/create_magento_order';
    const XML_PATH_SEND_EMAIL = 'web4pro_fastorder/general/send_email';
    const XML_PATH_EMAIL = 'web4pro_fastorder/general/email';
    const XML_PATH_CHANGE_ONEPAGE_CHECKOUT = 'web4pro_fastorder/checkout/change_onepage';
    const XML_PATH_ALLOW_COUNTRIES = 'web4pro_fastorder/general/allow_countries';
    const XML_PATH_DISPLAY_PHONE_IN_SALES_ORDERS = 'web4pro_fastorder/general/display_phone_in_sales_orders';
    const XML_PATH_FAST_ORDER_DESCRIPTION = 'web4pro_fastorder/general/fast_order_description';
    const XML_PATH_ONLY_CURRENT_PRODUCT = 'web4pro_fastorder/general/only_current_product';
    const XML_PATH_SHOW_EMAIL_FIELD = 'web4pro_fastorder/general/show_email_field';
    const XML_PATH_REG_GUEST_AS_CUSTOMER = 'web4pro_fastorder/general/register_guest_customer';
    const XML_PATH_SEND_EMAIL_TO_CUSTOMER = 'web4pro_fastorder/general/send_customer_email';

    /**
     * @var array
     */
    protected $phoneCodes = array();

    /**
     * Is Fast Order functionality enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Change onepage checkout with Fast Order
     *
     * @return bool
     */
    public function isChangeOnepageCheckout()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CHANGE_ONEPAGE_CHECKOUT);
    }

    /**
     * Save Fast Order in Magento
     *
     * @return bool
     */
    public function isSaveMagentoOrder()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CREATE_MAGENTO_ORDER);
    }

    /**
     * Send email address to admin
     *
     * @return bool
     */
    public function isSendEmail()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEND_EMAIL);
    }

    /**
     * @return bool
     */
    public function isSendEmailToCustomer()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEND_EMAIL_TO_CUSTOMER);
    }

    /**
     * get admin email address for send
     *
     * @return bool|string
     */
    public function getOrderNotificationEmail()
    {
        $email = false;
        if ($this->isSendEmail()) {
            $email = Mage::getStoreConfig(self::XML_PATH_EMAIL);
            if (!$email) {
                $email = Mage::getStoreConfig('trans_email/ident_general/email');
            }
        }
        return $email;
    }

    /**
     * get admin fast order description
     *
     * @return bool|string
     */
    public function getFastOrderDescription()
    {
        if($fastOrderDescription = Mage::getStoreConfig(self::XML_PATH_FAST_ORDER_DESCRIPTION)){
            return $fastOrderDescription;
        }
        return false;
    }

    /**
     * @return bool|mixed
     */
    public function showEmailField()
    {
        if($showEmailField = Mage::getStoreConfig(self::XML_PATH_SHOW_EMAIL_FIELD)){
            return $showEmailField;
        }
        return false;
    }

    public function registerGuestAsCustomer(){
        if($regGuest = Mage::getStoreConfig(self::XML_PATH_REG_GUEST_AS_CUSTOMER)){
            return $regGuest;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAllowCountries()
    {
        $codes = Mage::getStoreConfig(self::XML_PATH_ALLOW_COUNTRIES);
        return explode(',', $codes);
    }

    /**
     * @return Web4pro_Fastorder_Model_Resource_Country_Collection
     */
    public function getPhoneCodes()
    {
        $collection = Mage::getResourceModel('web4pro_fastorder/country_collection');
        $collection->addFieldToFilter('country_code', array('in' => $this->getAllowCountries()));
        $collection->setOrder('main_table.order', Varien_Data_Collection::SORT_ORDER_ASC);
        return $collection;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    public function getPhoneCodeByCountry($countryCode)
    {
        if (!isset($this->phoneCodes[$countryCode])) {
            $phoneCode = Mage::getModel('web4pro_fastorder/country')->load($countryCode, 'country_code');
            $this->phoneCodes[$countryCode] = $phoneCode->getPhoneCode();
        }
        return '+' . $this->phoneCodes[$countryCode];
    }

    /**
     * @param int $magentoOrderId
     * @return string
     */
    public function getPhoneNumberByOrder($magentoOrderId)
    {
        $order = Mage::getModel('web4pro_fastorder/order')->load($magentoOrderId, 'magento_order_id');
        return $order->getId() ? $this->getPhoneCodeByCountry($order->getCountry()) . $order->getPhone() : '';
    }

    /**
     * Is display phone number in Magento Sales Orders grid Order
     *
     * @return bool
     */
    public function isDisplayPhoneInSalesOrders()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_PHONE_IN_SALES_ORDERS);
    }

    public function isShowEmailField(){
        return $this->isSaveMagentoOrder() && $this->isGuest();
    }

    /**
     * @return bool
     * rewrite with normal correct core helper
     */
    public function isGuest()
    {
        return !Mage::helper('customer')->isLoggedIn();
//        return !(bool)Mage::getSingleton('customer/session')->getCustomerId();
    }

    /**
     *
     */
    public function getCart(){
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * @param $params
     * @return bool
     */
    public function initProduct($params) {
        $productId = (int) $params['product'];
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId())
                return $product;
        }
        return false;
    }

    public function getSession() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOnlyCurrent(){
        if($onlyCurrent = Mage::getStoreConfig(self::XML_PATH_ONLY_CURRENT_PRODUCT)){
            return $onlyCurrent;
        }
        return false;
    }
}
