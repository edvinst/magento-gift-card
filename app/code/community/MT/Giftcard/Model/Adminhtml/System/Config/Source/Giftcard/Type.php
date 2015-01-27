<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Type
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions($withEmpty = false)
    {
        return $this->toOptionArray();
    }

    public function toOptionArray()
    {
        $helper = Mage::helper('giftcard');
        return array(
            array(
                'label' => $helper->__('Virtual & Real'),
                'value' => 'virtual-real',
            ),
            array(
                'label' => $helper->__('Virtual'),
                'value' => 'virtual',
            ),
            array(
                'label' => $helper->__('Real'),
                'value' => 'real',
            ),

        );
    }

}