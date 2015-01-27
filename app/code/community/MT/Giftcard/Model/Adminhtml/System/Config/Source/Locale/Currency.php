<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Locale_Currency
{
    private $_options;

    private $__currencies;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $currencies = $this->getUsedCurrency();
            $options = array();
            if ($currencies) {
                foreach ($currencies as $currencyCode) {
                    $options[] = array(
                        'value' => $currencyCode,
                        'label' => $currencyCode,
                    );
                }
            }
            $this->_options = $options;
        }

        return $this->_options;
    }

    public function getUsedCurrency()
    {
        if (!$this->__currencies) {
            $currencyList = array();
            foreach (Mage::app()->getWebsites() as $website) {
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    foreach ($stores as $store) {
                        $currencyCode = $store->getCurrentCurrencyCode();
                        $currencyList[$currencyCode] = $currencyCode;
                    }
                }
            }
            $this->__currencies = $currencyList;
        }
        return $this->__currencies;
    }

}