<?php

class MT_Giftcard_Block_Payment_Giftcard_Form_Giftcard
    extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mt/giftcard/payment/giftcard/form/giftcard.phtml');
    }

    public function isFormVisible()
    {
        if (Mage::app()->getRequest()->getParam('is_form_visible') == 0
            && count($this->getAppliedGiftCardCollection()) == 0
        )
            return false;

        return true;
    }

    public function getAppliedGiftCardCollection()
    {
        $quoteId = Mage::getSingleton('checkout/cart')->getQuote()->getId();
        return Mage::getSingleton('giftcard/quote')->getGiftCardCollection($quoteId);
    }



    public function getAction()
    {
        return Mage::getUrl('giftcard/checkout_cart/giftCardAjax');
    }
}