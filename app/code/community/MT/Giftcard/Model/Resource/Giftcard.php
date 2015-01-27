<?php
class MT_Giftcard_Model_Resource_Giftcard extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/giftcard', 'entity_id');
    }
}