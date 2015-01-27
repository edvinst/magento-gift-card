<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_State
{
    protected $_status = array();

    public function __construct()
    {
        $helper = Mage::helper('giftcard');
        $this->_status = array(

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATE_READY_TO_PRINT),
                'value' => MT_Giftcard_Model_Giftcard::STATE_READY_TO_PRINT,
            ),
            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATE_PRINTING),
                'value' => MT_Giftcard_Model_Giftcard::STATE_PRINTING,
            ),
            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATE_PRINTED),
                'value' => MT_Giftcard_Model_Giftcard::STATE_PRINTED,
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