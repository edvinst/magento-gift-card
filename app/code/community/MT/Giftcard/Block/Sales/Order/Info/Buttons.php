<?php

class MT_Giftcard_Block_Sales_Order_Info_Buttons
    extends Mage_Core_Block_Template
{
    public function getGiftCardPdf()
    {
        return Mage::getUrl('giftcard/giftcard/pdf', array('id' => $this->getOrder()->getId()));
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }
}