<?php

class MT_Giftcard_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract
{
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = array();
        $giftCardValue = $item->getOptionByCode('giftcard_value');

        if ($giftCardValue) {
            $seriesId = $giftCardValue->getValue();
            $series = Mage::getModel('giftcard/series')->load($seriesId);

            $options[] = array(
                'label' => $this->__('Gift Card Value'),
                'value' => $series->getFormatedValue(),
            );
        }

        return $options;
    }
}