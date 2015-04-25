<?php

class MT_Giftcard_Block_Checkout_Cart_Giftcard
    extends Mage_Checkout_Block_Cart_Abstract
{
    private $__appliedGiftCardCollection = null;

    public function getAppliedGiftCards()
    {
        $quoteId = Mage::getSingleton('checkout/cart')->getQuote()->getId();
        return Mage::getSingleton('giftcard/quote')->getGiftCardCollection($quoteId);
    }

    public function getAmount()
    {
        $amount = 0;
        if (count($this->getAppliedGiftCards())) {
            foreach ($this->getAppliedGiftCards() as $giftCard){
                $amount+=$giftCard->getBalance();
            }
        }
        return $amount;
    }

    public function getCartTotalWithoutGiftCard()
    {
        $total = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();

        return $total;
    }

    public function isActive()
    {
        $helper = Mage::helper('giftcard');
        if (!$helper->isActive() || !Mage::getStoreConfig('giftcard/cart/form_in_cart'))
            return false;

        return Mage::helper('giftcard')->hasGiftCardProductInCart() == false;
    }

    public function getStatusCheckUrl()
    {
        return Mage::getUrl('giftcard/giftcard/status');
    }
}