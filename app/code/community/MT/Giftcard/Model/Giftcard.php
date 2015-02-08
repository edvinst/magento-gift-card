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

#    const TYPE_VIRTUAL_REAL = 'virtual-real';

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

    public function getFormatedBalance()
    {
        $balance = Mage::getModel('directory/currency')->format($this->getBalance(), array(
            'precision' => 2,
            'currency' => Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyCode()
        ), false);

        return $balance;
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

}