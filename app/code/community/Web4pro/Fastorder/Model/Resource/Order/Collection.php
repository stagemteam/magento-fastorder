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
class Web4pro_Fastorder_Model_Resource_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('web4pro_fastorder/order');
    }


    /**
     * Join Eav table
     * @param $attributeCode
     * @return Web4pro_Fastorder_Model_Resource_Order_Collection
     */
    public function joinCustomerAttribute($attributeCode)
    {
        //join that attribute table using attribute id and customer id
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
        if ($attribute) {
            $entityType = Mage::getModel('eav/entity_type')->loadByCode('customer');
            $entityTable = $this->getTable($entityType->getEntityTable()); //customer_entity
            if ($attribute->getBackendType() == 'static') {
                $table = $entityTable;
            } else {
                $table = $entityTable . '_' . $attribute->getBackendType(); //customer_entity_varchar
            }
            $tableAlias = "$table";
            $this->getSelect()->joinLeft($table,
                'main_table.customer_id = ' . $tableAlias . '.entity_id and ' . $tableAlias . '.attribute_id = ' . $attribute->getAttributeId(),
                array($attributeCode => $tableAlias . '.value')
            );
        }
        return $this;
    }
}