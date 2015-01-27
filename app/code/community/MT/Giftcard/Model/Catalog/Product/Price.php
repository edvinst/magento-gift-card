<?php

class MT_Giftcard_Model_Catalog_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($option = $product->getCustomOption('giftcard_value')) {
            $optionValue = $option->getValue();
            if (is_numeric($optionValue)) {
                $series = Mage::getModel('giftcard/series');
                $price = $series->getProductPrice($product, $optionValue);
                if ($price)
                    $finalPrice = $price;
            }
        }

        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
    }
}