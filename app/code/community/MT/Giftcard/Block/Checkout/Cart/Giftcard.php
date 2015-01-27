<?php

class MT_Giftcard_Block_Checkout_Cart_Giftcard
    extends Mage_Checkout_Block_Cart_Abstract
{
    private $__appliedGiftCardCollection = null;

    public function getAppliedGiftCards()
    {
        if ($this->__appliedGiftCardCollection == null) {
            $quote = Mage::getSingleton('checkout/cart')->getQuote();
            $giftCardsCodes = Mage::helper('giftcard')->getGiftCardCodeArray($quote->getMtGiftCard());
            if (count($giftCardsCodes) > 0) {
                $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection();
                $giftCardCollection->addFieldToFilter('code', array('in' => $giftCardsCodes));
                $this->__appliedGiftCardCollection = $giftCardCollection;
            }
        }
        return $this->__appliedGiftCardCollection;
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

    public function isVisible()
    {
        if (!Mage::helper('giftcard')->isActive() || !Mage::getStoreConfig('giftcard/cart/form_in_cart'))
            return false;

        $cart = Mage::getModel('checkout/cart')->getQuote();
        $items = $cart->getAllItems();
        if (count($items) == 0)
            return false;

        foreach ($items as $item) {
            if ($item->getProduct()->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
                return false;
        }

        return true;
    }
}