<?php
class MT_Giftcard_Model_Resource_Template_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/template');
    }
}