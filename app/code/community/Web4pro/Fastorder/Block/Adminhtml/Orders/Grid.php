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
class Web4pro_Fastorder_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('fastorder_list_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Web4pro_Fastorder_Model_Resource_Order_Collection
     */
    protected function _prepareCollectionBefore()
    {
        $fastOrderModel = Mage::getModel('web4pro_fastorder/order');
        $collection = $fastOrderModel->getCollection();
        $collection->getSelect()->joinLeft(
            array('country' => Mage::getModel('web4pro_fastorder/country')->getResource()
                ->getTable('web4pro_fastorder/country')),
            'country.country_code = main_table.country',
            array('country.country_code', 'country.phone_code')
        );
        $collection->joinCustomerAttribute('firstname');
        $collection->joinCustomerAttribute('lastname');
        return $collection;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_prepareCollectionBefore();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->_customFieldsOptions();
        return parent::_prepareColumns();
    }

    protected function _customFieldsOptions()
    {
        /** @var $helper Web4pro_Fastorder_Helper_Data */
        $helper = Mage::helper('web4pro_fastorder');
        $this->addColumn('entity_id', array(
            'header' => $helper->__('#'),
            'width' => '10px',
            'index' => 'entity_id',
            'filter_index' => 'main_table.entity_id',
        ));


        $this->addColumn('customer_id', array(
            'header' => $helper->__('Customer Name'),
            'index' => 'customer_id',
            'getter' => 'getGridCustomerName',
            'renderer' => 'web4pro_fastorder/adminhtml_orders_grid_renderer_customer',
        ));
        $this->addColumn('phone', array(
            'header' => $helper->__('Phone'),
            'index' => 'phone',
            'getter' => 'getFullPhoneNumber',
            'filter_index' => 'main_table.phone',
        ));

        $this->addColumn('country', array(
            'header' => $helper->__('country'),
            'index' => 'country',
            'width' => '155px',
            'empty_option' => $helper->__('All Codes'),
            'filter' => 'web4pro_fastorder/adminhtml_orders_grid_filter_filter',
            'options' => $helper->getPhoneCodes()->toOptionArray(),
            'filter_index' => 'main_table.country',
        ));

        $this->addColumn('create_date', array(
            'header' => $helper->__('Create  Date'),
            'index' => 'create_date',
            'filter_index' => 'create_date',
            'type' => 'datetime',
            'width' => '220px',
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/fast_order/view')) {
            $this->addColumn('action',
                array(
                    'header' => $helper->__('Action'),
                    'type' => 'action',
                    'actions' => array(
                        array(
                            'caption' => $helper->__('View'),
                            'url' => $this->getUrl('*/fastorder/view', array('id' => '$entity_id')),
                            'field' => 'entity_id'
                        ),
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'web4pro_fastorder',
                    'is_system' => true,
                    'width' => 100
                ));
        }

    }

    /**
     * Return row URL for js event handlers
     *
     * @param null $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/fast_order/view')) {
            return $this->getUrl('*/fastorder/view', array('id' => $row->getEntityId()));
        }
        return null;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/fastorder/grid', array('_current' => true));
    }
}
