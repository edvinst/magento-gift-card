<?php

class MT_Giftcard_Model_Giftcard extends Mage_Core_Model_Abstract
{
    const STATE_READY_TO_PRINT = 'ready_to_print';

    const STATE_PRINTED = 'printed';

    const STATE_PRINTING = 'printing';

    const STATUS_NEW = 'new';

    const STATUS_PENDING = 'pending';

    const STATUS_ACTIVE = 'active';

    const STATUS_SOLD = 'sold';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_EXPIRED = 'expired';

    const GIFT_CARD_CODE_MAX_LENGTH = 255;

    const TYPE_VIRTUAL = 'virtual';


    protected function _construct()
    {
        $this->_init('giftcard/giftcard');
    }

    public function codeExist($code)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->setPageSize(1);
        return $collection->count() == 1;
    }


    public function setExpiredAt($date = '')
    {
        if($date == '')
            $date = $this->calcExpiredAt();
        return parent::setExpiredAt($date);
    }

    public function getExpiredAt()
    {
        $expiredAt = parent::getExpiredAt();
        if (empty($expiredAt) && $this->getLifetime() > 0) {
            $expiredAt = $this->calcExpiredAt();
        }

        return $expiredAt;
    }

    public function calcExpiredAt()
    {
        $lifeTime = $this->getLifetime();
        if ($lifeTime == 0)
            return '';

        return date('Y-m-d H:i:s', strtotime('+'.$lifeTime.'days'));
    }

    public function isCodeActive($code)
    {
        if (empty($code) || strlen($code) > self::GIFT_CARD_CODE_MAX_LENGTH)
            return false;

        $collection = $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('status', self::STATUS_SOLD)
            ->setPageSize(1);
        return $collection->count() == 1;

    }

    public function loadByCode($code)
    {
        if (empty($code) || strlen($code) > self::GIFT_CARD_CODE_MAX_LENGTH)
            return $this;

        $collection = $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->setPageSize(1);
        if ($collection->count() == 0)
            return $this;

        return parent::load($collection->getFirstItem()->getId());
    }

    public function getFormatedValue()
    {
        $value = Mage::getModel('directory/currency')->format($this->getValue(), array(
            'precision' => 0,
            'currency' => Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyCode()
        ), false);

        return $value;
    }


    public function getTemplate()
    {
        if (!$this->getTemplateId())
            return null;

        $template = Mage::getModel('giftcard/template')->load($this->getTemplateId());
        if (!$template->getId())
            return null;

        return $template;
    }

    public function getBalance($currencyCode = '')
    {
        $balance = $this->getData('balance');
        $giftCardCurrency = $this->getCurrency();
        if (empty($currencyCode) || $currencyCode == $giftCardCurrency) {
            return $balance;
        }

        $convertedBalance = Mage::helper('directory')->currencyConvert(
            $balance,
            $giftCardCurrency,
            $currencyCode
        );
        $convertedBalance = number_format($convertedBalance, 2);

        return $convertedBalance;
    }

    public function setBalance($balance, $currencyCode = '')
    {
        if ($currencyCode == '') {
            return $this->setData('balance', $balance);
        }
        $giftCardCurrency = $this->getCurrency();
        if ($currencyCode != $giftCardCurrency) {
            $balance = Mage::helper('directory')->currencyConvert(
                $balance,
                $currencyCode,
                $giftCardCurrency
            );
            $balance = number_format($balance, 2);
        }

        return $this->setData('balance', $balance);
    }

    public function getFormatedBalance($currencyCode = '')
    {
        if (empty($currencyCode)) {
            $currencyCode = $this->getCurrency();
        }
        $balance = $this->getBalance($currencyCode);

        $formatedBalance = Mage::getModel('directory/currency')->format(
            $balance,
            array(
                'precision' => 2,
                'currency' =>  $currencyCode
            ), false);

        return $formatedBalance;
    }

    public function isSold()
    {
        return $this->getStatus() == self::STATUS_SOLD;
    }

    public function discount($balance, $currency)
    {
        $this->setBalance($balance, $currency);
        if ($balance <= 0) {
            $this->setStatus(MT_Giftcard_Model_Giftcard::STATUS_INACTIVE);
        }
        $this->save();
    }

    public function refund($refund, $currency)
    {
        $giftCardBalance = $this->getBalance($currency);
        if (
            $this->getStatus() == MT_Giftcard_Model_Giftcard::STATUS_INACTIVE
            && $giftCardBalance == 0
        ) {
            $this->setStatus(MT_Giftcard_Model_Giftcard::STATUS_SOLD);
        }
        $returnAmount = ($giftCardBalance+$refund);
        $this->setBalance($returnAmount, $currency);
        $this->save();
    }

    public function getCurrency($code = '')
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }
}