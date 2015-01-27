<?php
class MT_Giftcard_Model_Resource_Series extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/series', 'entity_id');
    }


}