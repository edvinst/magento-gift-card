<?php

class MT_Giftcard_Block_Checkout_Payment_Giftcard
    extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('mt/giftcard/checkout/payment/giftcard.phtml');
    }

    public function getControllerUrl()
    {
        return Mage::getUrl('giftcard/checkout_cart');
    }
}