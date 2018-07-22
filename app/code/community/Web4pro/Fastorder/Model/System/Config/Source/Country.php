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
class Web4pro_Fastorder_Model_System_Config_Source_Country
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {

            //for back comp with < 1.6
            $alias = '`directory/country`';
            $codesCollection = Mage::getResourceModel('web4pro_fastorder/country_collection');
            $codesCollection->join('directory/country', "$alias.country_id = main_table.country_code");
            $codesCollection->setOrder('main_table.order', Varien_Data_Collection::SORT_ORDER_ASC);
            $this->_options = $codesCollection->toOptionArray();
        }
        $options = $this->_options;
        return $options;
    }
}
