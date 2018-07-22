<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 09.04.15
 * Time: 17:55
 */
class Web4pro_Fastorder_Model_Resource_Customer extends Mage_Customer_Model_Resource_Customer
{

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Model_Customer $customer
     * @throws Exception
     * @return Model_Resource_Customer
     */
    protected function _beforeSave(Varien_Object $customer)
    {
        // set confirmation key logic
        $store = Mage::app()->getStore();
        $customer->setWebsiteId($store->getWebsiteId());
        $customer->setCreatedIn($store->getName());
        if ($customer->getForceConfirmed()) {
            $customer->setConfirmation(null);
        } elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
            $customer->setConfirmation($customer->getRandomConfirmationKey());
        }
        // remove customer confirmation key from database, if empty
        if (!$customer->getConfirmation()) {
            $customer->setConfirmation(null);
        }

        return $this;
    }
}