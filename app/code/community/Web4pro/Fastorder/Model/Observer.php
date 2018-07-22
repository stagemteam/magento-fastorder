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
class Web4pro_Fastorder_Model_Observer
{
    /**
     * @return Web4pro_Fastorder_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('web4pro_fastorder');
    }

    /**
     * @dispatch checkout_type_onepage_save_order_after
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function saveMagentoOrderId(Varien_Event_Observer $observer)
    {
        $params = Mage::app()->getRequest()->getParams();
        $fastOrderInstance = Mage::registry('fastorder_order_instance');
        if ($fastOrderInstance && $fastOrderInstance instanceof Web4pro_Fastorder_Model_Order) {
            $order = $observer->getEvent()->getOrder();
            if(!empty($params['comment'])){
                $order->addStatusHistoryComment($params['comment']);
            }
            $fastOrderInstance->setMagentoOrderId($order->getId());
            $fastOrderInstance->save();
        }
        Mage::unregister('fastorder_order_instance');

        return $this;
    }

    /**
     * Change standard OnePage checkout with One Click Order checkout
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function changeOnepageCheckout(Varien_Event_Observer $observer)
    {
        switch ($observer->getEvent()->getName()) {
            case 'controller_action_layout_generate_blocks_after':
                $action = $observer->getEvent()->getAction();
                $layout = $observer->getEvent()->getLayout();
                if ($action->getFullActionName() == 'checkout_cart_index' && $this->_helper()->isEnabled() && $this->_helper()->isChangeOnepageCheckout()) {
                    $block = $layout->getBlock('checkout.cart.methods');
                    if ($block && $block instanceof Mage_Core_Block_Abstract) {
                        $block->unsetChild('checkout.cart.methods.onepage');
                    }
                    $block = $layout->getBlock('checkout.cart.top_methods');
                    if ($block && $block instanceof Mage_Core_Block_Abstract) {
                        $block->unsetChild('checkout.cart.methods.onepage');
                    }
                }

                break;
            case 'controller_action_predispatch_checkout_onepage_index':
                if ($this->_helper()->isEnabled() && $this->_helper()->isChangeOnepageCheckout()) {
                    $action =  $observer->getEvent()->getControllerAction();
                    if ($action) {
                        $action->getResponse()->setRedirect(Mage::getUrl('checkout/cart/index'));
                    }
                }

                break;
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addPhoneColumnToSalesCollection(Varien_Event_Observer $observer)
    {
        if ($this->_helper()->isDisplayPhoneInSalesOrders()) {
            $collection = $observer->getEvent()->getOrderGridCollection();
            $collection->getSelect()->joinLeft(
                array('web4pro_orders' => $collection->getTable('web4pro_fastorder/order')),
                'web4pro_orders.magento_order_id=main_table.entity_id',
                array('phone', 'country')
            );
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addPhoneColumnToSalesGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!isset($block)) {
            return $this;
        }
        if ($block->getType() == 'adminhtml/sales_order_grid' && $this->_helper()->isDisplayPhoneInSalesOrders()) {
            $block->addColumnAfter('customer_phone',
                array(
                    'header' => $this->_helper()->__('Customer Phone'),
                    'type' => 'text',
                    'index' => 'phone',
                    'filter_index' => 'web4pro_orders.phone',
                    'frame_callback' => array($this, 'decoratePhoneColumnInSalesGrid'),
                ),
                'shipping_name'
            );
            $block->sortColumnsByOrder();
        }
        return $this;
    }

    /**
     * Decorator for phone column in sales grid
     */
    public function decoratePhoneColumnInSalesGrid($value, $row, $column, $isExport)
    {
        return $value ? $this->_helper()->getPhoneCodeByCountry($row->getCountry()) . $value : '';
    }


    /**
     * insert fast order form into product page
     * only for 1 column layout
     * @param $observer
     */
    public function insertFastOrderBlockAfter($observer)
    {
       /* $_block = $observer->getEvent()->getBlock();
        $_blockName = $_block->getModuleName();
        if($_blockName != 'Mage_Catalog'){
            return;
        }
        $typeCol = false;
        $layout = $_block->getLayout();
        if($layout){
            $temp = $layout->getBlock('root')->getTemplate();
            $typeCol = strpos($temp,'1column');
        }
        $_type = $_block->getType();

        if ($_type == "catalog/product_list_upsell" && false !== $typeCol) {
            $transport = $observer->getEvent()->getTransport();
            $html = $transport->getHtml();
            $childHtml = Mage::getSingleton('core/layout')->createBlock('web4pro_fastorder/form')
                ->setTemplate('web4pro/fastorder/form.phtml')->toHtml();
            $html = $childHtml . $html;
            $transport->setHtml($html);
        }*/
    }

    public function saveFastOrderPlaceAfter($observer){
        $order = $observer->getOrder();
        Mage::register('magento_order', $order);
    }
}
