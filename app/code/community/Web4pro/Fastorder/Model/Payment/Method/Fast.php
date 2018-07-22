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


/**
 * Default fast order payment method
 */
class Web4pro_Fastorder_Model_Payment_Method_Fast extends Mage_Payment_Model_Method_Abstract
{
    protected $_canUseCheckout = false;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'web4pro_fastorder';

    /**
     * @inheritdoc
     */
    public function canUseForCountry($country)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable($quote = null)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isApplicableToQuote($quote, $checksBitMask)
    {
        return true;
    }
}
