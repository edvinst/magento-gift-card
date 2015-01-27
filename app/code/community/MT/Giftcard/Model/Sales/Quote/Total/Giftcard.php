<?php

class MT_Giftcard_Model_Sales_Quote_Total_Giftcard
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    private $__calculator = null;

    public function __construct()
    {
        $this->__calculator = Mage::getSingleton('giftcard/sales_discount');
    }

    protected function _getCalc()
    {
        return $this->__calculator;
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        $discount = $this->_getCalc()->getQuoteTotalDiscount($address);

        $this->_addAmount($discount);
        $this->_addBaseAmount($discount);

        $address->setMtGiftCardTotal($discount);
        $address->setBaseMtGiftCardTotal($discount);
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
