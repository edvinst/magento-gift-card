<?php
class MT_Giftcard_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/template', 'entity_id');
    }
}