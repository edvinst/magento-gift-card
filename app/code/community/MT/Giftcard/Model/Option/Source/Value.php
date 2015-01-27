<?php

class MT_Giftcard_Model_Option_Source_Value
{
    public function getValues($product = null)
    {
        $values = array();
        if ($product) {
            $seriesCollection = Mage::getModel('giftcard/series')->getCollectionByProduct($product->getId());;
            if ($seriesCollection) {
                foreach ($seriesCollection as $series) {
                    $values[$series->getId()] = $series->getValue();
                }
            }
        }
        return $values;
    }
}