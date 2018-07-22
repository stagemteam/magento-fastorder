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
class Web4pro_Fastorder_Block_Abstract extends Mage_Core_Block_Template
{

    /**
     * @return Web4pro_Fastorder_Helper_Data
     */
    public function getModuleHelper()
    {
        return Mage::helper('web4pro_fastorder');
    }

    protected function _toHtml()
    {
        if (!$this->getModuleHelper()->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @return bool
     * rewrite with normal correct core helper
     */
    public function isGuest()
    {
        return !Mage::helper('customer')->isLoggedIn();
    }

}