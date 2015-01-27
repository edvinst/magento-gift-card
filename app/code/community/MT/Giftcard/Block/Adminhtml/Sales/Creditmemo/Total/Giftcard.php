<?php

class MT_Giftcard_Block_Adminhtml_Sales_Creditmemo_Total_Giftcard
    extends Mage_Core_Block_Abstract
{

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $value = $order->getMtGiftCardTotal();
        if ($value != 0) {
            $parent->addTotal(new Varien_Object(array(
                'code'  => 'mt_gift_card_total',
                'value' => 5,
                'block_name' => 'gift_card_totals'
            )));
        }
        return $this;
    }

    public function getOrder()
    {
        $parent = $this->getParentBlock();
        return $parent->getOrder();
    }
}