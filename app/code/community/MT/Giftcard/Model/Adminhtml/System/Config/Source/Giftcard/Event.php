<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Event
{
    public function toOptionArray()
    {
        $helper = Mage::helper('giftcard');
        return array(
            array(
                'label' => $helper->__('After Invoice Created'),
                'value' => 'after_invoice_created',
            ),
            array(
                'label' => $helper->__('After Order Completed'),
                'value' => 'after_order_completed',
            ),

        );
    }

}