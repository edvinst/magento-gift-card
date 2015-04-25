<?php

class MT_Giftcard_Block_Adminhtml_Sales_Creditmemo_Create_Total_Giftcard
    extends Mage_Core_Block_Template
{
    private $__giftCardOrderCollection = null;

    public function initTotals()
    {
        $giftCards = $this->getGiftCardOrderCollection();
        if ($giftCards->count() == 0)
            return $this;

        $total = new Varien_Object(array(
            'code'      => 'mt_gift_card_total',
            'block_name' => 'gift_card_total'
        ));

        $parent = $this->getParentBlock();
        $parent->addTotal($total);
        return $this;
    }

    public function getOrder()
    {
        $parent = $this->getParentBlock();
        return $parent->getOrder();
    }

    public function getGiftCardOrderCollection()
    {
        if ($this->__giftCardOrderCollection == null) {
            $order = $this->getOrder();
            $collection = Mage::getModel('giftcard/order')->getCollectionByOrderId($order->getId());
            $this->__giftCardOrderCollection = $collection;
        }
        return $this->__giftCardOrderCollection;
    }
}