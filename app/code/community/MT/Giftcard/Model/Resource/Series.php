<?php
class MT_Giftcard_Model_Resource_Series extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/series', 'entity_id');
    }

}