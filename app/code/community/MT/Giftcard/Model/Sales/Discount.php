<?php

class MT_Giftcard_Model_Sales_Discount
{
    private $__giftCardCollection = null;

    protected function _getQuote()
    {
        return Mage::helper('checkout/cart')->getQuote();
    }

    public function getGiftCardCollection()
    {
        if ($this->__giftCardCollection == null) {
            $giftCardsCodes = Mage::helper('giftcard')->getGiftCardCodeArray($this->_getQuote()->getMtGiftCard()); //print_r($giftCardsCodes);exit;
            if (count($giftCardsCodes) > 0) {
                //only with status sold and balance more than 0
                $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
                    ->addFieldToFilter('code', array('in' => $giftCardsCodes))
                    ->addFieldToFilter('balance', array('gt' => 0));

                $this->__giftCardCollection = $giftCardCollection;
            }
        }
        return $this->__giftCardCollection;
    }

    public function getTotalDiscount()
    {
        $total = 0;
        if (count($this->getGiftCardCollection()) > 0) {
            foreach ($this->getGiftCardCollection() as $giftCard){
                $total-=$giftCard->getBalance();
            }
        }

        return $total;
    }



    public function getQuoteTotalDiscount($address)
    {
        $giftCardDiscount = 0;
        $total = 0;
        $totals = $address->getAllTotalAmounts();
        if (count($totals) > 0) {
            foreach ($totals as $amount) {
                $total+=$amount;
            }
        }
        if ($total > 0) {
            $giftCardDiscount = $this->getTotalDiscount();
            if ($giftCardDiscount < $total*-1)
                $giftCardDiscount = $total*-1;
        }

        return $giftCardDiscount;
    }
}