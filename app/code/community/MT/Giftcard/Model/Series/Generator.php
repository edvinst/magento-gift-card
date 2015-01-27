<?php

class MT_Giftcard_Model_Series_Generator extends Mage_Core_Model_Abstract
{
    protected $_generatedCount = 0;

    protected $_generatedGiftCardCollection = null;

    const MAX_PROBABILITY_OF_GUESSING = 0.25;
    const MAX_GENERATE_ATTEMPTS = 10;

    public function generatePool()
    {

        $this->_generatedCount = 0;
        $size = $this->getQty();
        $status = $this->getStatus();
        $maxProbability = self::MAX_PROBABILITY_OF_GUESSING;
        $maxAttempts =  self::MAX_GENERATE_ATTEMPTS;

        $giftCard = Mage::getModel('giftcard/giftcard');
        $giftCardSeries = Mage::getModel('giftcard/series');
        $giftCardSeries->load($this->getSeriesId());
        if (!is_numeric($giftCardSeries->getId()))
            Mage::throwException(Mage::helper('giftcard')->__('Unable to create gift cards. Series must be selected.'));

        $chars = count(Mage::helper('giftcard')->getCharset($this->getFormat()));

        $length = (int) $this->getLength();
        $maxCodes = pow($chars, $length);
        $probability = $size / $maxCodes;
        //increase the length of Code if probability is low
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = pow($chars, $length);
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            $this->setLength($length);
        }

        $this->_generatedGiftCardCollection = new Varien_Data_Collection();

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            while ($attempt < $maxAttempts) {
                $code = $this->generateCode();
                if (!$giftCard->codeExist($code)) {
                    break;
                }
                $attempt++;
                if ($attempt >= $maxAttempts)
                    Mage::throwException(Mage::helper('giftcard')->__('Unable to create requested GiftCard Qty. Please check settings and try again.'));
            }

            $giftCard = Mage::getModel('giftcard/giftcard');
            $giftCard->setCode($code);
            $giftCard->setStatus($status);
            $giftCard->setSeriesId($giftCardSeries->getId());

            $giftCard->setValue($giftCardSeries->getValue());
            $giftCard->setBalance($giftCardSeries->getValue());
            $giftCard->setStoreId($giftCardSeries->getStoreId());
            $lifeTime = $giftCardSeries->getLifetime();
            $giftCard->setLifetime($lifeTime);
            if ($lifeTime != 0 && $status == MT_Giftcard_Model_Giftcard::STATUS_SOLD)
                $giftCard->setExpiredAt();
            if ($status == MT_Giftcard_Model_Giftcard::STATUS_NEW)
                $giftCard->setState(MT_Giftcard_Model_Giftcard::STATE_READY_TO_PRINT);
            $giftCard->setCurrency($giftCardSeries->getCurrency());
            $giftCard->setTemplateId($giftCardSeries->getTemplateId());
            $giftCard->save();
            $this->_generatedGiftCardCollection->addItem($giftCard);
            $this->_generatedCount++;
        }
        return $this;
    }

    public function validateData($data)
    {
        return !empty($data) && !empty($data['qty']) && !empty($data['series_id']) && !empty($data['status'])
        && !empty($data['length']) && !empty($data['format'])
        && (int)$data['qty'] > 0 && (int) $data['series_id'] > 0
        && (int) $data['length'] > 0;
    }

    public function getGeneratedCount()
    {
        return $this->_generatedCount;
    }

    public function generateCode()
    {
        $format  = $this->getFormat();
        $length  = max(1, (int) $this->getLength());
        $split   = max(0, (int) $this->getDash());
        $suffix  = $this->getSuffix();
        $prefix  = $this->getPrefix();

        $splitChar = $this->getDelimiter();
        $charset = Mage::helper('giftcard')->getCharset($format);

        $code = '';
        $charsetSize = count($charset);
        for ($i=0; $i<$length; $i++) {
            $char = $charset[mt_rand(0, $charsetSize - 1)];
            if ($split > 0 && ($i % $split) == 0 && $i != 0) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }

        $code = $prefix . $code . $suffix;
        return $code;
    }

    public function getDelimiter()
    {
        //TODO add to system config
        return '-';
    }

    public function getCollection()
    {
        return $this->_generatedGiftCardCollection;
    }

}