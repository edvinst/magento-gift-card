<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Format
{
    public function toOptionArray()
    {
        $helper = Mage::helper('giftcard');

        return array(
            array(
                'label' => $helper->__('Alphanumeric'),
                'value' => 'alphanumeric',
            ),
            array(
                'label' => $helper->__('Alphabetical'),
                'value' => 'alphabetical',
            ),
            array(
                'label' => $helper->__('Numeric'),
                'value' => 'numeric',
            ),
        );
    }
}