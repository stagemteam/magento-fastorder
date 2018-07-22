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
?>
<?php
/**
 * @method int getQuoteId
 * @method int getCustomerId
 * @method int getMagentoOrderId
 * @method int getStoreId
 * @method string getPhone
 * @method string getComment
 * @method string getCountry
 * @method string getCreateDate
 */
class Web4pro_Fastorder_Model_Order extends Mage_Core_Model_Abstract
{

    /**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('web4pro_fastorder/order');
    }

    /**
     * get full phone number
     * @return string
     */
    public function getFullPhoneNumber()
    {
        if ($this->getPhoneCode()) {
            return '+' . $this->getPhoneCode() . ' ' . $this->getPhone();
        }
        return '+' . $this->getCountryModel()->getPhoneCode() . ' ' . $this->getPhone();
    }

    /**
     * get customer for order
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        if (!$customer->getId()) {
            $customer->setIsGuest(true);
        }
        return $customer;
    }

    /**
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getModel('sales/quote')->getCollection()
            ->addFieldToFilter('entity_id', $this->getQuoteId())
            ->getFirstItem();
    }


    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getMagentoOrder()
    {
        return $this->getMagentoOrderId() ?
            Mage::getModel('sales/order')->load($this->getMagentoOrderId())
            : null;
    }

    /**
     * @return Web4pro_Fastorder_Model_Country
     */
    public function getCountryModel()
    {
        return Mage::getModel('web4pro_fastorder/country')->load($this->getCountry(), 'country_code');
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return Mage::app()->getLocale()->storeDate(
            $this->getStoreId(),
            strtotime($this->getCreateDate()),
            true
        );
    }


}