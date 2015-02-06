<?php
class MT_Giftcard_Model_Resource_Template extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/template', 'entity_id');
    }
}