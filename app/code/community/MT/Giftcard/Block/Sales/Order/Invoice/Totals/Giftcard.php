<?php

class MT_Giftcard_Block_Sales_Order_Invoice_Totals_Giftcard
    extends Mage_Sales_Block_Order_Invoice_Totals
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $value = $order->getMtGiftCardTotal();
        if ($value < 0) {
            $giftCardDiscounts = unserialize($order->getMtGiftCard());
            foreach ($giftCardDiscounts as $key => $discount) {
                if (!isset($discount['discount']) || $discount['discount'] == 0)
                    continue;
                $total = new Varien_Object(array(
                    'code'  => 'mt_gift_card_total_'.$key,
                    'value' => $discount['discount'],
                    'base_value' => $discount['discount'],
                    'label' => $this->__('Gift Card (%s)',$discount['code']),
                    'field' => 'mt_gift_card_total'
                ));
                $parent->addTotal($total, 'mt_gift_card_total_'.$key);
            }
        }
        return $this;
    }
}