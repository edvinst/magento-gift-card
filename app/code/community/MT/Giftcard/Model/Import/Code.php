<?php

//require_once('lib/PHPExcel.php');

class MT_Giftcard_Model_Import_Code extends MT_Giftcard_Model_Import_Abstract
{
    protected $_requiredFields = array('code');

    private $__emptyFieldsData = array();

    public function setEmptyFieldsData($data)
    {
        $this->__emptyFieldsData = $data;
    }

    public function getEmptyFieldsData()
    {
        return $this->__emptyFieldsData;
    }

    protected function import($data)
    {
        $helper = Mage::helper('giftcard');
        $itemCount = count($data);
        if ($itemCount == 0)
            throw new Exception($helper->__('Empty file'));

        $emptyFieldsData = $this->getEmptyFieldsData();
        $giftCard = Mage::getModel('giftcard/giftcard');
        $giftCard->setValue($emptyFieldsData['value']);
        $giftCard->setBalance($emptyFieldsData['value']);
        $giftCard->setStatus($emptyFieldsData['status']);
        $giftCard->setCurrency($emptyFieldsData['currency']);
        $giftCard->setSeriesId($emptyFieldsData['series_id']);
        $giftCard->setTemplateId($emptyFieldsData['template_id']);
        $giftCard->setLifetime($emptyFieldsData['lifetime']);
        $giftCard->setStoreId($emptyFieldsData['store_id']);
        if (!empty($emptyFieldsData['expired_at']))
            $giftCard->setExpiredAt($emptyFieldsData['expired_at']);
        if ($giftCard->getExpiredAt() == '' && is_numeric($giftCard->getLifetime()) && $giftCard->getStatus() == MT_Giftcard_Model_Giftcard::STATUS_SOLD) {
            $giftCard->setExpiredAt();
        }

        foreach ($data as $row) {

            if (!isset($row['code'])) {
                $this->_skipped++;
                continue;
            }
            if ($giftCard->codeExist($row['code'])) {
                if (isset($emptyFieldsData['skip']) && $emptyFieldsData['skip']) {
                    $this->_skipped++;
                    continue;
                } else {
                    throw new Exception($helper->__('Gift Card with Code: %s already exist', $row['code']));
                }
            }

            $giftCard->setCode($row['code']);
            $giftCard->setId(null);
            $giftCard->save();

            $this->_imported++;
        }
    }
}