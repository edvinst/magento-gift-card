<?php

class MT_Giftcard_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract
{
    public function getCustomOptions($item)
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