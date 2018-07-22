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
class Web4pro_Fastorder_Model_Resource_Country extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('web4pro_fastorder/country', 'entity_id');
    }


}