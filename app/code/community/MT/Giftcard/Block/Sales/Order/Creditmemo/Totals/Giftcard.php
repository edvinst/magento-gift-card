<?php

class MT_Giftcard_Block_Sales_Order_Creditmemo_Totals_Giftcard
    extends Mage_Sales_Block_Order_Creditmemo_Totals
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
                $totalDiscounted = new Varien_Object(array(
                    'code'  => 'mt_gift_card_total_discount_'.$item->getId(),
                    'value' => -$item->getDiscount(),
                    'base_value' => -$item->getBaseDiscount(),
                    'label' => $this->__('Discounted from Gift Card (%s)',$item->getGiftCardCode()),
                    'field' => 'mt_gift_card_total_discount_'.$item->getId()
                ));

                $totalRefunded = new Varien_Object(array(
                    'code'  =>  'mt_gift_card_total_refund_'.$item->getId(),
                    'value' => $item->getRefund(),
                    'base_value' => $item->getBaseRefund(),
                    'label' => $this->__('Refunded to Gift Card (%s)',$item->getGiftCardCode()),
                    'field' => 'mt_gift_card_total_refund_'.$item->getId()
                ));

                $parent = $this->getParentBlock();
                $parent->addTotal($totalDiscounted, 'mt_gift_card_total_discount_'.$item->getId());
                $parent->addTotal($totalRefunded, 'mt_gift_card_total_refund_'.$item->getId());
            }
        }

        return $this;
    }
}