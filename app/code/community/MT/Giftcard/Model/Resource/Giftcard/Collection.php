<?php
class MT_Giftcard_Model_Resource_Giftcard_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/giftcard');
    }

}