<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Template extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    private $__options = null;

    public function getAllOptions($withEmpty = false)
    {
        if ($this->__options == null) {
            $options = array(
                array(
                    'value' => '',
                    'label' => ''
            ));

            $giftCardSeriesCollection = Mage::getModel('giftcard/template')->getCollection();
            if ($giftCardSeriesCollection->count() > 0) {
                foreach ($giftCardSeriesCollection as $giftCardSeries) {
                    $skip[$giftCardSeries->getValue()] = 1;
                    $options[] = array(
                        'value' => $giftCardSeries->getId(),
                        'label' => $giftCardSeries->getName()
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
        return $this->getAllOptions();
    }

}