<?php

//require_once('lib/PHPExcel.php');

class MT_Giftcard_Model_Export_Object extends MT_Giftcard_Model_Export_Abstract
{
    protected $_object = null;

    public function setObject(Varien_Object $object)
    {
        $this->_object = $object;
    }

    public function getObject()
    {
        return $this->_object;
    }

    public function exportItem($item)
    {
        $this->getAdapter()->addNext($item);
    }

    protected function exportHeadline()
    {
        return true;
    }


}