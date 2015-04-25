<?php

class MT_Giftcard_Model_Sales_Quote_Total_Giftcard
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        $giftCardQuote = Mage::getModel('giftcard/quote');
        $quote = $address->getQuote();
        $baseDiscount = $giftCardQuote->calculateDiscount($address, $quote->getBaseCurrencyCode());
        $discount =  $giftCardQuote->calculateDiscount($address, $quote->getQuoteCurrencyCode());

        $this->_addAmount($discount);
        $this->_addBaseAmount($baseDiscount);

        $address->setMtGiftCardTotal($discount);
        $address->setBaseMtGiftCardTotal($baseDiscount);
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {

        if ($address->getMtGiftCardTotal() < 0) {
            $giftCardDiscount = $address->getMtGiftCardTotal();
            $description = $address->getGiftCardDescription();
            if (strlen($description)) {
                $title = Mage::helper('giftcard')->__('Gift Card Discount (%s)', $description);
            } else {
                $title = Mage::helper('giftcard')->__('Gift Card Discount');
            }

            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $giftCardDiscount
            ));
        }
        return $this;
    }
}
