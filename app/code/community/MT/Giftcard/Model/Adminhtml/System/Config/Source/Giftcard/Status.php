<?php

class MT_Giftcard_Model_Adminhtml_System_Config_Source_Giftcard_Status
{
    protected $_status = array();

    public function __construct()
    {
        $helper = Mage::helper('giftcard');
        $this->_status = array(
            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_NEW),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_NEW,
            ),

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_ACTIVE),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_ACTIVE,
            ),

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_PENDING),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_PENDING,
            ),

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_SOLD),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_SOLD,
            ),

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_INACTIVE),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_INACTIVE,
            ),

            array(
                'label' => $helper->__(MT_Giftcard_Model_Giftcard::STATUS_EXPIRED),
                'value' => MT_Giftcard_Model_Giftcard::STATUS_EXPIRED,
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