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
class Web4pro_Fastorder_Block_Form extends Web4pro_Fastorder_Block_Abstract
{
    public function getPhoneCodeHtml($name, $class = '', $withCountryName = true, $emptyLabel = 'Code')
    {
        $codesCollection = $this->getModuleHelper()->getPhoneCodes();
        $codes = $codesCollection->toOptionArray($withCountryName);

        if (count($codes) > 1) {
            $options = array_merge(
                array(array(
                    'value' => '',
                    'label' => $emptyLabel,
                )),
                $codes
            );

            $selectedValue = '';

            $html = $this->getLayout()->createBlock('core/html_select')
                ->setName($name)
                ->setId('fastorder-phone-code')
                ->setClass($class)
                ->setValue($selectedValue)
                ->setOptions($options)
                ->getHtml();
        } else {
            $item = $codesCollection->getFirstItem();
            $html = "<input type=\"hidden\" name=\"$name\" value=\"{$item->getCountryCode()}\" /><span class=\"$class\">+{$item->getPhoneCode()}</span> ";
        }
        return $html;
    }

    /**
     * need o render email field. Only for guest and if magento order is enabled
     */
    public function isShowEmailField()
    {
        return ($this->getModuleHelper()->isSaveMagentoOrder() && $this->isGuest());
    }

}