<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Series extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    private $__options = null;

    public function getAllOptions($withEmpty = false)
    {
        $isAdmin = Mage::app()->getStore()->isAdmin();
        if ($this->__options == null) {
            $options = array(
                array(
                    'value' => '',
                    'label' => ''
            ));

            $giftCardSeriesCollection = Mage::getModel('giftcard/series')->getCollection();
            if ($giftCardSeriesCollection->count() > 0) {
                foreach ($giftCardSeriesCollection as $giftCardSeries) {
                    $skip[$giftCardSeries->getValue()] = 1;
                    if (!$isAdmin)
                        $label = Mage::getModel('directory/currency')->format($giftCardSeries->getValue(), array('currency' => $giftCardSeries->getCurrencyCode()), false);
                     else
                        $label = $giftCardSeries->getName().' ('.Mage::getModel('directory/currency')->format($giftCardSeries->getValue(), array('currency' => $giftCardSeries->getCurrencyCode()), false).')';
                    $options[] = array(
                        'value' => $giftCardSeries->getId(),
                        'label' => $label
                    );
                }
            }
            $this->__options = $options;
        }
        return $this->__options;
    }


    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        exit('b');
        return $this->getAllOptions();
    }

}