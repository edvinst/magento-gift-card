<?php

class MT_Giftcard_Block_Payment_Giftcard_Form
    extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mt/giftcard/payment/giftcard/form.phtml');
    }

    public function getControllerUrl()
    {
        return Mage::getUrl('giftcard/checkout_cart');
    }

    public function isActive()
    {
        $helper = Mage::helper('giftcard');
        if (!$helper->isActive())
            return false;

        return Mage::helper('giftcard')->hasGiftCardProductInCart() == false;
    }
}