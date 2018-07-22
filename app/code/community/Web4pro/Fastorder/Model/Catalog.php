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
class Web4pro_Fastorder_Model_Catalog extends Mage_Core_Model_Abstract
{

    public function prepareQuickCartItems($product, $request)
    {
        $items = array();
        $cartCandidates = $product->getTypeInstance(true)
            ->prepareForCartAdvanced($request, $product, Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL);
        $quote = Mage::getModel('sales/quote');
        $quote->addProductAdvanced($product,$request,Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL);


        $customer=Mage::getSingleton('customer/session')->getCustomer();
        if($customer->getId())
            $quote->setCustomer($customer);

        return $quote;
    }
}