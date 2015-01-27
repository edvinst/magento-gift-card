<?php

class MT_Giftcard_Model_Checkout_Giftcard
{
    private $__quoteGiftCardCollection = null;

    public function getQuoteGiftCardCollection()
    {
        if ($this->__quoteGiftCardCollection == null) {
            $quote = Mage::getSingleton('checkout/cart')->getQuote();
            $giftCardsCodes = Mage::helper('giftcard')->getGiftCardCodeArray($quote->getMtGiftCard());
            if (count($giftCardsCodes) > 0) {
                $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection();
                $giftCardCollection->addFieldToFilter('code', array('in' => $giftCardsCodes));
                $this->__quoteGiftCardCollection = $giftCardCollection;
            }
        }
        return $this->__quoteGiftCardCollection;
    }



}