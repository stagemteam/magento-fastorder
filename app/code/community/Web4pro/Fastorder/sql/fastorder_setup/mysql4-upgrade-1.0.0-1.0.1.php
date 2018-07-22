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

$installer = $this;

$installer->startSetup();

$installer->run("delete web4pro_fastorder_orders.*  FROM web4pro_fastorder_orders
left join sales_flat_order  on sales_flat_order.entity_id = web4pro_fastorder_orders.magento_order_id
where sales_flat_order.entity_id IS NULL");
$installer->run("ALTER TABLE `{$installer->getTable('web4pro_fastorder/order')}`
ADD FOREIGN KEY `fast_orders_to_full_orders` (`magento_order_id` ) REFERENCES `sales_flat_order` (`entity_id` ) ON DELETE CASCADE ");

$installer->endSetup();
