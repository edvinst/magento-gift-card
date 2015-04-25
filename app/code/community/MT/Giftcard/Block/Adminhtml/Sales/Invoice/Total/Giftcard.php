<?php

class MT_Giftcard_Block_Adminhtml_Sales_Invoice_Total_Giftcard
    extends Mage_Core_Block_Abstract
{
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $value = $order->getMtGiftCardTotal();
        if ($value < 0) {
            $collection = Mage::getModel('giftcard/order')->getCollectionByOrderId($order->getId());
            foreach ($collection as $item) {
                if ($item->getDiscount() == 0) {
                    continue;
                }

                $totalDiscounted = new Varien_Object(array(
                    'code'  => 'mt_gift_card_total_discount_'.$item->getId(),
                    'value' => -$item->getDiscount(),
                    'base_value' => -$item->getBaseDiscount(),
                    'label' => $this->__('Discounted from Gift Card (%s)',$item->getGiftCardCode()),
                    'field' => 'mt_gift_card_total_discount_'.$item->getId()
                ));

                $parent = $this->getParentBlock();
                $parent->addTotal($totalDiscounted, 'mt_gift_card_total_discount_'.$item->getId());
            }
        }
        return $this;
    }
}