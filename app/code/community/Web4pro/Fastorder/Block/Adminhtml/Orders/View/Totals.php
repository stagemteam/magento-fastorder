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
class Web4pro_Fastorder_Block_Adminhtml_Orders_View_Totals extends Mage_Adminhtml_Block_Sales_Totals //Mage_Adminhtml_Block_Sales_Order_Abstract
{

    /**
     * Retrieve order model object
     *
     * @return Web4pro_Fastorder_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('order');
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getSource()
    {
        return $this->getOrder()->getQuote();
    }

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['count'] = new Varien_Object(array(
            'code' => 'count',
            'strong' => true,
            'is_formated' => true,
            'value' => $this->getSource()->getItemsCount(),
            'base_value' => $this->getSource()->getItemsCount(),
            'label' => $this->helper('web4pro_fastorder')->__('Total items'),
            'area' => 'footer'
        ));

        $magentoOrder = $this->getOrder()->getMagentoOrder();

        if ($magentoOrder) {
            $this->_totals['paid'] = new Varien_Object(array(
                'code' => 'paid',
                'strong' => true,
                'value' => $magentoOrder->getTotalPaid(),
                'base_value' => $magentoOrder->getBaseTotalPaid(),
                'label' => $this->helper('sales')->__('Total Paid'),
                'area' => 'footer'
            ));
        }
        return $this;
    }
}
