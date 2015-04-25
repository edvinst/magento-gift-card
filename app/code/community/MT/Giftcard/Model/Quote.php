<?php

class MT_Giftcard_Model_Quote extends Mage_Core_Model_Abstract
{

    private $__error = '';

    private $__giftCardCollection = array();

    protected function _construct()
    {
        $this->_init('giftcard/quote');
    }

    public function isGiftCardCodeValid($quoteId, $giftCardCode)
    {
        $helper = Mage::helper('giftcard');
        $codeLength = strlen($giftCardCode);
        $isCodeLengthValid = $codeLength > 0 && $codeLength <= MT_Giftcard_Model_Giftcard::GIFT_CARD_CODE_MAX_LENGTH;

        if (!$isCodeLengthValid) {
            $this->setError($helper->__('Bad gift card code.'));
            return false;
        }

        $giftCard = Mage::getModel('giftcard/giftcard');
        $giftCard->loadByCode($giftCardCode);
        if (!$giftCard->getId() || !$giftCard->isSold()) {
            $this->setError($helper->__('Bad gift card code.'));
            return false;
        }

        if ($this->isAddedToQuote($quoteId, $giftCard->getId())) {
            $this->setError($helper->__('This gift card already added.'));
            return false;
        }

        return true;
    }

    public function setError($errorMsg)
    {
        $this->__error = $errorMsg;
    }

    public function getError()
    {
        return $this->__error;
    }

    public function addGiftCardByCode($quoteId, $giftCardCode)
    {
        if (!$this->isGiftCardCodeValid($quoteId, $giftCardCode)) {
            throw new Mage_Core_Exception($this->getError());
        }

        $giftCard = Mage::getModel('giftcard/giftcard');
        $giftCard->loadByCode($giftCardCode);

        $this->setQuoteId($quoteId);
        $this->setGiftCardId($giftCard->getId());
        $this->save();

        if (!$this->getId()) {
            return false;
        }

        return true;
    }

    public function removeGiftCardArrayFromQuote($quoteId, array $codes)
    {
        if (count($codes) == 0) {
            return true;
        }

        foreach ($codes as $code) {
            $this->removeGiftCardByCode($quoteId, $code);
        }

        return true;
    }

    public function removeGiftCardByCode($quoteId, $code)
    {
        $giftCard = Mage::getModel('giftcard/giftcard')->loadByCode($code);
        if (!$giftCard->getId()) {
            return false;
        }

        $giftCardQuote = Mage::getModel('giftcard/quote')->getCollection()
            ->addFieldToFilter('gift_card_id', $giftCard->getId())
            ->addFieldToFilter('quote_id', $quoteId);

        if ($giftCardQuote->count() == 0) {
            return false;
        }

        foreach ($giftCardQuote as $item) {
            $item->delete();
        }

        return true;
    }

    public function removeAllGiftCardFromQuote($quoteId)
    {
        $giftCardQuote = Mage::getModel('giftcard/quote')->getCollection()
            ->addFieldToFilter('quote_id', $quoteId);

        if ($giftCardQuote->count() == 0) {
            return false;
        }

        foreach ($giftCardQuote as $item) {
            $item->delete();
        }

        return true;
    }

    public function isAddedToQuote($quoteId, $giftCardId)
    {
        $giftCardCollection = $this->getCollection()
            ->addFieldToFilter('gift_card_id', $giftCardId)
            ->addFieldToFilter('quote_id', $quoteId);
        return count($giftCardCollection) == 1;
    }

    public function getGiftCardCollection($quoteId)
    {
        if (!isset($this->__giftCardCollection[$quoteId])) {
            $quoteCollection = Mage::getModel('giftcard/quote')->getCollection()
                ->addFieldToFilter('quote_id', $quoteId);
            if (count($quoteCollection) == 0) {
                $this->__giftCardCollection[$quoteId] = null;
            }
            $gifCardIds = array();
            foreach ($quoteCollection as $item) {
                $gifCardIds[] = $item->getGiftCardId();
            }

            $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $gifCardIds))
                ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_SOLD)
                ->addFieldToFilter('balance', array('gt' => 0));

            $this->__giftCardCollection[$quoteId] = $giftCardCollection;
        }

        return $this->__giftCardCollection[$quoteId];
    }

    public function calculateDiscount(Mage_Sales_Model_Quote_Address $address, $currencyCode)
    {
        $giftCardDiscount = 0;
        $total = 0;
        $totals = $address->getAllTotalAmounts();
        $quote = $address->getQuote();
        if (count($totals) > 0) {
            foreach ($totals as $amount) {
                $total+=$amount;
            }
        }
        if ($total > 0) {
            $giftCardDiscount = 0;
            $appliedGiftCardCollection = $this->getGiftCardCollection($quote->getId());
            if (count($appliedGiftCardCollection) > 0) {
                foreach ($appliedGiftCardCollection as $giftCard){
                    $balance = $giftCard->getBalance($currencyCode);
                    $giftCardDiscount-=$balance;
                }
            }

            //if total less than gift cards balance
            if ($giftCardDiscount < $total*-1)
                $giftCardDiscount = $total*-1;
        }

        return $giftCardDiscount;
    }

    public function saveDiscount($quoteId, $giftCardId, $discount, $baseDiscount)
    {
        $giftCardQuote = Mage::getModel('giftcard/quote')->getCollection()
            ->addFieldToFilter('gift_card_id', $giftCardId)
            ->addFieldToFilter('quote_id', $quoteId);

        foreach ($giftCardQuote as $item) {
            $item->setDiscount($discount);
            $item->setBaseDiscount($baseDiscount);
            $item->save();
        }
    }
}