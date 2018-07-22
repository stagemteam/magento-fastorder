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
class Web4pro_Fastorder_Block_Adminhtml_Orders_Grid_Filter_Filter extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected function _getOptions()
    {
        $options = $this->getColumn()->getOptions();
        array_unshift($options, array('value' => '', 'label' => $this->getColumn()->getEmptyOption()));
        return $options;
    }
}
