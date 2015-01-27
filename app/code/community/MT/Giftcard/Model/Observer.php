<?php

class MT_Giftcard_Model_Observer
{

    public function allowZeroCreditMemo($observer)
    {
        if (!Mage::helper('giftcard')->isActive())
            return;

        $creditMemo = $observer->getCreditmemo();
        $order = $creditMemo->getOrder();
        if($order->getBaseMtGiftCardTotal()!=0)
            $creditMemo->setAllowZeroGrandTotal(1);
    }

    public function forcedCreditMemo($observer)
    {
        $order = $observer->getOrder();
        if ($order->getBaseMtGiftCardTotal() != 0) {
            $order->setForcedCanCreditmemo(1);
        }
    }

    public function createGiftCard($event)
    {
        $order = $event->getOrder();
        Mage::getModel('giftcard/giftcard_action')->assignGiftCardToOrder($order);
    }

    public function activeGiftCard($observer)
    {
        if (!Mage::helper('giftcard')->isActive())
            return;

        $order = $observer->getInvoice()->getOrder();
        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('order_id', $order->getId())
            ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_PENDING);

        if ($giftCardCollection->count() == 0)
            return;

        foreach ($giftCardCollection as $giftCard) {
            $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_SOLD);
            $giftCard->setExpiredAt();
            $giftCard->save();
        }

        return;
    }

    public function deactivateGiftCard($observer)
    {
        if (!Mage::helper('giftcard')->isActive())
            return;

        $order = $observer->getCreditmemo()->getOrder();
        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('order_id', $order->getId())
            ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_SOLD);

        if ($giftCardCollection->count() == 0)
            return;

        foreach ($giftCardCollection as $giftCard) {
            $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_PENDING);
            $giftCard->save();
        }

        return;
    }

    public function catalogProductSaveAfter($observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $request = $event->getRequest();
        $links = Mage::app()->getRequest()->getPost('links');

        if (isset($links['giftcard']))
            Mage::getModel('giftcard/series_action')->assignGiftCardSeriesToProduct($product->getId(), Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['giftcard']));
    }

    public function discountGiftCardBalance($observer)
    {
        if (!Mage::helper('giftcard')->isActive())
            return;

        $order = $observer->getEvent()->getOrder();
        $quote = $order->getQuote();
        $address = $quote->getShippingAddress();

        $discountedFromGiftCard = $address->getMtGiftCardTotal()*-1;
        $giftCardData = $order->getMtGiftCard();
        $giftCards = Mage::helper('giftcard')->getGiftCardCodeArray($giftCardData);
        $giftCardData = unserialize($giftCardData);

        if ($discountedFromGiftCard == 0)
            return;

        if (count($giftCards) == 0)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Gift Card is no longer available to use.'));


        $totalBalance = 0;
        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('code', array('in' => $giftCards))
            ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_SOLD)
            ->addFieldToFilter('balance', array('gt' => 0));


        foreach ($giftCardCollection as $giftCard) {
            if (!is_numeric($giftCard->getId()))
                throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Bad gift card code'));
            if ($giftCard->getStatus() != MT_Giftcard_Model_Giftcard::STATUS_SOLD)
                throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Gift Card "%s" is no longer available to use.', $giftCard->getCode()));
            $totalBalance += $giftCard->getBalance();
        }

        if ($totalBalance < $discountedFromGiftCard)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Not enough balance in gift card'));

        $discountLabel = '';
        foreach ($giftCardCollection as $giftCard) {
            $giftCardBalance = $giftCard->getBalance();
            if ($discountedFromGiftCard >= $giftCardBalance)
                $discount = $giftCardBalance;
            else
                $discount = $discountedFromGiftCard;

            $balance = $giftCardBalance - $discount;
            $giftCard->setBalance($balance);
            if ($giftCard->getBalance() <= 0)
                $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_INACTIVE);

            $giftCard->save();
            $discountedFromGiftCard -= $discount;
            $discountLabel .= $giftCard->getCode().' (-'.Mage::helper('core')->currency($discount, true, false).'), ';

            foreach ($giftCardData as $key => $item) {
                if ($item['code'] == $giftCard->getCode())
                    $giftCardData[$key]['discount'] = -$discount;
            }

            if ($totalBalance <= 0)
                break;
        }
        $order->setMtGiftCardDescription(rtrim($discountLabel, ', '));
        $order->setMtGiftCard(serialize($giftCardData));
        $order->setForcedCanCreditmemo(0);
    }

    public function refundGiftCardBalance($observer)
    {
        $params = Mage::app()->getRequest()->getParam('gift_card');

        if (count($params) == 0)
            return;

        $order = $observer->getCreditmemo()->getOrder();
        $giftCards = unserialize($order->getMtGiftCard());

        if (count($giftCards) == 0)
            return;

        foreach ($params as $giftCardId => $refundValue)
        {
            $giftCard = Mage::getModel('giftcard/giftcard')->load($giftCardId);
            if (!$giftCard->getId())
                continue;

            if ($giftCard->getStatus() == MT_Giftcard_Model_Giftcard::STATUS_INACTIVE
                && $giftCard->getBalance() == 0
                && $refundValue != 0
            )
                $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_SOLD);

            $giftCard->setBalance(($giftCard->getBalance()+$refundValue));
            $giftCard->save();

            foreach ($giftCards as $key => $giftCardData) {
                if ($giftCardData['code'] == $giftCard->getCode())
                    $giftCards[$key]['refunded'] = $refundValue;
            }
        }

        $creditMemo = $observer->getCreditmemo();
        $creditMemo->getOrder()->setMtGiftCard(serialize($giftCards));
        $creditMemo->getOrder()->setForcedCanCreditmemo(0);
        $creditMemo->setMtGiftCard(serialize($giftCards));

        return;
    }
}