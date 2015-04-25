<?php
class MT_Giftcard_Model_Resource_Quote extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/quote', 'entity_id');
    }
}