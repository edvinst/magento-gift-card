<?php

//require_once('lib/PHPExcel.php');

class MT_Giftcard_Model_Import_Giftcard extends MT_Giftcard_Model_Import_Abstract
{
    protected $_requiredFields = array(
        'code', 'value', 'balance', 'status','active', 'expired_at','store_id'
    );

    protected function import($data)
    {

        $helper = Mage::helper('giftcard');
        $itemCount = count($data);
        if ($itemCount == 0)
            throw new Exception($helper->__('Empty file'));

        $giftCard = Mage::getModel('giftcard/giftcard');
        foreach ($data as $row) {

            if (!isset($row['code'])) {
                $this->_skipped++;
                continue;
            }

            if ($giftCard->codeExist($row['code'])) {
                $this->_skipped++;
                continue;
            }

            foreach ($row as $field => $value) {
               $giftCard->setData($field, $value);
            }

            $giftCard->setId(null);
            $giftCard->save();
            $this->_imported++;
        }
    }

}