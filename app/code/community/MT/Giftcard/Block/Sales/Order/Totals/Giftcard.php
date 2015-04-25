<?php

class MT_Giftcard_Block_Sales_Order_Totals_Giftcard
    extends Mage_Sales_Block_Order_Totals
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $value = $order->getMtGiftCardTotal();
        if ($value < 0) {
            $collection = Mage::getModel('giftcard/order')->getCollectionByOrderId($order->getId());
            foreach ($collection as $item) {
                if ($item->getDiscount() == 0)
                    continue;
                $total = new Varien_Object(array(
                    'code'  => 'mt_gift_card_total_'.$item->getId(),
                    'value' => -$item->getDiscount(),
                    'base_value' => -$item->getBaseDiscount(),
                    'label' => $this->__('Discounted from Gift Card (%s)',$item->getGiftCardCode()),
                    'field' => 'mt_gift_card_total'
                ));
                $parent->addTotal($total, 'mt_gift_card_total_'.$item->getId());
            }
        }

        return $this;
    }
}