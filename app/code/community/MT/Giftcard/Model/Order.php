<?php

class MT_Giftcard_Model_Order extends Mage_Core_Model_Abstract
{
    private $__giftCardCollection = array();

    private $__itemCollection = array();

    protected function _construct()
    {
        $this->_init('giftcard/order');
    }

    public function addGiftCardByCode($orderId, $giftCardCode, $discount = 0, $baseDiscount = 0)
    {

        if ($this->isAddedToOrder($orderId, $giftCardCode)) {
            throw new Exception('Something is wrong. Gift card already exist in order!');
        }

        $this->setOrderId($orderId);
        $this->setGiftCardCode($giftCardCode);
        $this->setDiscount($discount);
        $this->setBaseDiscount($baseDiscount);
        $this->save();

        if (!$this->getId()) {
            return false;
        }

        return true;
    }

    public function isAddedToOrder($orderId, $giftCardCode)
    {
        $giftCardCollection = $this->getCollection()
            ->addFieldToFilter('gift_card_code', $giftCardCode)
            ->addFieldToFilter('order_id', $orderId);
        return count($giftCardCollection) == 1;
    }

    public function getGiftCardCollection($orderId)
    {
        if (!isset($this->__giftCardCollection[$orderId])) {
            $quoteCollection = Mage::getModel('giftcard/order')->getCollection()
                ->addFieldToFilter('order_id', $orderId);
            if (count($quoteCollection) == 0) {
                $this->__giftCardCollection[$orderId] = null;
            }
            $gifCardIds = array();
            foreach ($quoteCollection as $item) {
                $gifCardIds[] = $item->getGiftCardId();
            }

            $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $gifCardIds));

            $this->__giftCardCollection[$orderId] = $giftCardCollection;
        }

        return $this->__giftCardCollection[$orderId];
    }

    public function getCollectionByOrderId($orderId)
    {
        if (!isset($this->__itemCollection[$orderId])) {
            $collection = Mage::getModel('giftcard/order')->getCollection()
                ->addFieldToFilter('order_id', $orderId);

            if ($collection->count() == 0) {
                $this->__itemCollection[$orderId] = null;
            }
            $this->__itemCollection[$orderId] = $collection;
        }
        return $this->__itemCollection[$orderId];
    }

    public function getGiftCard()
    {
        if (!$this->getId()) {
            return null;
        }

        $gifCard = Mage::getModel('giftcard/giftcard')->loadByCode($this->getGiftCardCode());
        return $gifCard;
    }
}