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
class Web4pro_Fastorder_Model_ServiceQuote extends Mage_Sales_Model_Service_Quote
{

    protected function _validate()
    {
        if (Mage::registry('fastorder_ignore_quote_validation')) {
            return $this;
        }
        return parent::_validate();
    }
}