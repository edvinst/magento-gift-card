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

    public function createGiftCard($event)
    {
        $order = $event->getOrder();
        Mage::getModel('giftcard/giftcard_action')->assignGiftCardToOrder($order);

        Mage::dispatchEvent('giftcard_active_after', array('order' => $order));
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
        Mage::dispatchEvent('giftcard_active_after', array('order' => $order));
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
            $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_INACTIVE);
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
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $baseCurrency = $quote->getBaseCurrencyCode();
        $quoteCurrency = $quote->getQuoteCurrencyCode();
        $addressesCollection = $quote->getAddressesCollection();
        $giftCardQuote = Mage::getModel('giftcard/quote');

        foreach ($addressesCollection as $address) {
            $discountLabel = '';
            $totalGiftCardBalance = 0;

            //in base currency
            $discountedFromGiftCard = $address->getBaseMtGiftCardTotal() * -1;
            if ($discountedFromGiftCard == 0)
                continue;

            $giftCardCollection = $giftCardQuote->getGiftCardCollection($quote->getId());

            $totalGiftCardBalance = $this->getTotalBalance($giftCardCollection, $baseCurrency);
            if ($totalGiftCardBalance < $discountedFromGiftCard)
                throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Not enough balance in gift card'));

            foreach ($giftCardCollection as $giftCard) {
                $giftCardBalance = $giftCard->getBalance($baseCurrency);
                if ($discountedFromGiftCard >= $giftCardBalance) {
                    $discount = $giftCardBalance;
                } else {
                    $discount = $discountedFromGiftCard;
                }

                //in base currency
                $balance = $giftCardBalance - $discount;
                $giftCard->discount($balance, $baseCurrency);
                $discountedFromGiftCard -= $discount;

                if ($quoteCurrency == $baseCurrency) {
                    $quoteDiscount = $discount;
                } else {
                    $quoteDiscount = Mage::helper('directory')->currencyConvert(
                        $discount,
                        $baseCurrency,
                        $quoteCurrency
                    );
                    $quoteDiscount = number_format($quoteDiscount, 2);
                }

                Mage::getModel('giftcard/order')->addGiftCardByCode($order->getId(), $giftCard->getCode(), $quoteDiscount, $discount);

                if ($totalGiftCardBalance <= 0 || $discountedFromGiftCard <= 0)
                    break;
            }
        }
    }

    protected function getTotalBalance($giftCardCollection, $currency)
    {
        $totalGiftCardBalance = 0;
        foreach ($giftCardCollection as $giftCard) {
            $totalGiftCardBalance += $giftCard->getBalance($currency);
        }
        return $totalGiftCardBalance;
    }

    public function refundGiftCardBalance($observer)
    {
        $giftCardOrderItemIds = Mage::app()->getRequest()->getParam('gift_card_order_item');
        if (count($giftCardOrderItemIds) == 0)
            return;

        $totalRefund = 0;
        $totalBaseRefund = 0;
        $creditMemo = $observer->getCreditmemo();
        $order = $creditMemo->getOrder();
        $baseCurrency = $order->getBaseCurrencyCode();
        $orderCurrency = $order->getOrderCurrencyCode();

        foreach ($giftCardOrderItemIds as $itemId => $baseRefundValue)
        {
            $giftCardOrder = Mage::getModel('giftcard/order')->load($itemId);
            if (!$giftCardOrder->getId()) {
                continue;
            }
            $giftCard = $giftCardOrder->getGiftCard();
            if (!$giftCard->getId() || $baseRefundValue == 0) {
                continue;
            }
            $giftCard->refund($baseRefundValue, $baseCurrency);

            if ($orderCurrency == $baseCurrency) {
                $refundValue = $baseRefundValue;
            } else {
                $refundValue = Mage::helper('directory')->currencyConvert(
                    $baseRefundValue,
                    $baseCurrency,
                    $orderCurrency
                );
                $refundValue = number_format($refundValue, 2);
            }
            $giftCardOrder->setBaseRefund($baseRefundValue);
            $giftCardOrder->setRefund($refundValue);
            $giftCardOrder->save();
            $totalRefund+=$refundValue;
            $totalBaseRefund+=$baseRefundValue;
        }
        return;
    }
}