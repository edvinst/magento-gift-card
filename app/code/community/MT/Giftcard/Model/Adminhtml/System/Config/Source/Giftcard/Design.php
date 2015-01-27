<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Design
{
    protected $_status = array();

    public function __construct()
    {
        $helper = Mage::helper('giftcard');
        $this->_status = array(

            array(
                'label' => 'Default',
                'value' => 'default',
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_status;
    }

    public function toKeyValueArray()
    {
        $data = array();
        foreach ($this->_status as $status) {
            $data[$status['value']] = $status['label'];
        }
        return $data;
    }
}