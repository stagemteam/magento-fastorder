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
class Web4pro_Fastorder_IndexController extends Mage_Core_Controller_Front_Action
{
    const CHARS_LOWERS                          = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS                          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected $_errors = array();

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $helper = Mage::helper('web4pro_fastorder');
        if (!$helper->isEnabled()) {
            return $this->_ajaxEnd(array(
                'error' => $helper->__('Please, use standard checkout for order product.')
            ));
        }
    }

    /**
     * Save order in Magento
     * @return bool
     */
    protected function _saveMagentoOrder()
    {

        $onepage = $this->getOnepage();
        try {
            $onepage->savePayment(array(
                'method' => 'web4pro_fastorder',
            ));
            $onepage->getQuote()->collectTotals()->save();
            Mage::register('fastorder_ignore_quote_validation', true, true);
            Mage::register('save_magento_order_from_fastorder', true);
            $onepage->saveOrder();
            Mage::unregister('fastorder_ignore_quote_validation');
            return true;
        } catch (Exception $e) {
            $this->_errors[] = $message = $e->getMessage();
        }
        return false;
    }

    /**
     * @param $data
     * @return Web4pro_Fastorder_Model_Order
     */
    protected function _saveOrderInfo($data)
    {
        $helper = Mage::helper('web4pro_fastorder');
        $isGuest = $this->_isGuest($data);
        $needRegister = (bool)$helper->registerGuestAsCustomer();
        $customerId = null;

        if(isset($data['email'])) {
            $existingCustomer = $this->_getCustomerModel()->loadByEmail($data['email']);
            $needRegister = ($customerId = $existingCustomer->getId()) ? false : $needRegister;
        }
        if($isGuest && $needRegister){
            $customerId = $this->_registerCustomer($data);
        }

        $model = Mage::getModel('web4pro_fastorder/order');
        $model->setData($data);
        $model->setCustomerId($customerId);
        $model->setQuoteId($this->getOnepage()->getQuote()->getId());
        $model->setStoreId(Mage::app()->getStore()->getId());
        $model->setCreateDate(date('Y-m-d h:i:s'));
        $model->setTelephone($data['phone']);
        $model->setCustomerEmail($data['email']);
        $quote = $this->getOnepage()->getQuote();

        // save for Guest
        $newBillingAddress = Mage::getModel('sales/quote_address');
        $newBillingAddress->setEmail($data['email']);
        if(isset($data['phone'])){
            $newBillingAddress->setTelephone($data['phone']);
        }
        if (!$customerId && isset($data['email'])) {
            if(!$customerId){
                $quote->setCustomerId(null);
                $quote->setCustomerEmail($newBillingAddress->getEmail());
                $quote->setCustomerIsGuest(true);
                $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            }

        } else{
                $customer = $this->_getCustomerModel()->load($customerId);
                $quote->setCustomerId($customerId);
                $quote->setCustomerEmail($newBillingAddress->getEmail());
                $quote->setCustomerIsGuest(false);
                $quote->setCustomerGroupId($customer->getGroupId());
        }
        $quote->setBillingAddress($newBillingAddress)->save();
        Mage::register('fastorder_order_instance', $model);
        $this->_saveMagentoOrder();
        return $model;
    }



    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Is current customer guest
     * @return bool
     */
    protected function _isGuest()
    {
        return !(bool)Mage::getSingleton('customer/session')->getCustomerId();
    }

    protected function _ajaxEnd($data)
    {
        return $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($data)
        );
    }

    /**
     * @return string
     */
    protected function _getErrors()
    {
        return implode("\n", $this->_errors);
    }

    /**
     * validate entered data
     * @param $data
     * @return bool
     */
    protected function _validateData($data)
    {

        $helper = Mage::helper('web4pro_fastorder');
        $showEmailField = $helper->showEmailField();
        $this->_filterInput($data);

        if (!$data['phone'] || !$data['phone']) {
            $this->_errors[] = $helper->__("Phone empty, fill required fields");
        }

        if($showEmailField && isset($data['email']) && !Zend_Validate::is($data['email'], 'Zend_Validate_EmailAddress')) {
                $this->_errors[] = $helper->__('Please enter a valid email address.');
        }

        return !(bool)count($this->_errors);
    }

    /**
     * Filter data
     *
     * @param array $data
     */
    protected function _filterInput($data)
    {
        foreach ($data as &$v) {
            $v = trim(strip_tags($v));
        }
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Save and process order
     */
    public function saveOrderAction()
    {
        $helper = Mage::helper('web4pro_fastorder');
        $cart = $helper->getCart();
        $params = $this->getRequest()->getParams();

        if (!$this->_validateData($params)) {
            return $this->_ajaxEnd(array(
                'error' => $this->_getErrors()
            ));
        }

        if(isset($params['onlycurrent']) && $params['onlycurrent'] == 1){
            $this->_cleanCart();
        }
        $result = array();
        if(isset($params['product']) && !empty($params['product'])){
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
                    $params['qty'] = $filter->filter($params['qty']);
                }
                $product = $helper->initProduct($params);

                if ($product) {
                    $addRes = $cart->addProduct($product, $params);
                    if(!$addRes){
                        return $this->_ajaxEnd(array(
                            'error' => Mage::helper('checkout')->__('Something wrong. Please check all fields of product and submit fast order again.')
                        ));
                    }
                    $cart->save();
                    $helper->getSession()->setCartWasUpdated(true);
                    Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest()));

                } else {
                    Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__('Such product has not been found!'));
                }
            } catch (Mage_Core_Exception $e) {
                $result['hasOptions'] = true;
                return $this->_ajaxEnd(array(
                    'error' => $e->getMessage()
                ));
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('checkout')->__('Cannot add item to shopping cart!'));
                return $this->_ajaxEnd(array(
                    'error' => $e->getMessage()
                ));
            }
        }

        Mage::getSingleton('checkout/session')->unsFastOrderId();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }
        if (!$this->getOnepage()->getQuote()->hasItems() || $this->getOnepage()->getQuote()->getHasError()) {
            return $this->_ajaxEnd(array(
                'error' => $helper->__('Please, add item to Cart before order.')
            ));
        }


        $order = $this->_saveOrderInfo($params);

        $order->save();

        if ($this->_getErrors()) {
            if ($order->getId()) {
                $order->delete();
            }
            return $this->_ajaxEnd(array(
                'error' => $this->_getErrors()
            ));
        }

        $this->getOnepage()->getQuote()->setIsActive(0)->save();
        if ($helper->isSendEmail()) {
            Mage::helper('web4pro_fastorder/email')->sendOrderEmailToAdmin($order);
        }

        Mage::getSingleton('checkout/session')->setFastOrderId($order->getId());
        return $this->_ajaxEnd(array(
            'success' => true,
            'redirect' => Mage::getUrl('web4pro_fastorder/index/success')
        ));
    }

    /**
     * Success page
     */
    public function successAction()
    {
        if (!Mage::getSingleton('checkout/session')->getFastOrderId()) {
            return $this->_redirect('checkout/cart/index');
        }
        $this->loadLayout();

        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    /**
     *  remove all items from cart
     */
    protected function _cleanCart(){
        Mage::getSingleton('checkout/cart')->truncate();
    }

    protected function _registerCustomer($data){
        $customerId = null;
        $customerModel = $this->_getCustomerModel();
        $customerModel->setWebsiteId(Mage::app()->getWebsite('admin')->getId());
        $customerModel->setStore(Mage::app()->getStore());
        if(isset($data['email']) && $data['email']){
            $customer = $customerModel->loadByEmail($data['email']);
            $customerId = $customer->getId();
            if($customerId){
                return $customerId;
            }
        }

        if(isset($data['phone']) && $data['phone']){
            $customerId = $this->_loadCustomerByPhone($data);
        }
        if(!$customerId){
            $customerId = $this->_createCustomer($data);
        }
        return $customerId;
    }

    protected function _loadCustomerByPhone($data){

        $addressCollection = Mage::getModel("Customer/Entity_Address_Collection");
        $addressCollection->addAttributeToSelect('*');
        $addressCollection->addAttributeToFilter('country_id', $data['country']);
        $addressCollection->addAttributeToFilter('telephone', $data['phone']);
        $address = $addressCollection->getFirstItem();
        $id = $address->getData('parent_id');

        return $id;
    }

    protected function _createCustomer($data){
        $pwdLength = 8;
        $customer = $this->_getCustomerModel();
        $customer->setFirstname('Guest');
        $customer->setLastname('Guest');
        $customer->setCreatedAt(date('Y-m-d h:i:s'));
        if(isset($data['email']) && !empty($data['email'])){
            $customer->setEmail($data['email']);
        }
        $customer->setPassword($customer->generatePassword($pwdLength));
        try{
            $customer->save();
            if($customer->getEmail()){
                $customer->sendNewAccountEmail(); //send confirmation email to customer
            }
        } catch (Exception $e){
            Mage::log('Create customer error: '. $e->getMessage(),null,'fast-order.log');
        }

        //for newsly created customer set the phone number
        if(isset($data['phone']) && !empty($data['phone'])){
            $customerAddress = Mage::getModel('customer/address');
            $customerAddress->setCustomerId($customer->getId())
                ->setFirstname($customer->getFirstname())
                ->setLastname($customer->getLastname())
                ->setCountryId($data['country'])
                ->setCity('Guest')
                ->setStreet('Guest')
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1')
                ->setTelephone($data['phone']);
            try {
                $customerAddress->save();
            }
            catch (Exception $ex) {
                Mage::log('Save customer phone error: '. $ex->getMessage(),null,'fast-order.log');
            }
        }
        if($customer->getId()){
            return $customer->getId();
        }
        return false;
    }

    protected function _getCustomerModel()
    {
        return Mage::getModel('customer/customer')->setStore(Mage::app()->getStore());
    }
}
