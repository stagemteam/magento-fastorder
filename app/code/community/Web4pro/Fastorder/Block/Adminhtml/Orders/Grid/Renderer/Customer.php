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
class Web4pro_Fastorder_Block_Adminhtml_Orders_Grid_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getCustomerId()) {
            $name = $row->getFirstname() . ' ' . $row->getLastname();
            $result = sprintf('<a href="%s">%s</a>', Mage::getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())), $name);
        } else {
            $result = 'Guest';
        }

        return $result;
    }
}