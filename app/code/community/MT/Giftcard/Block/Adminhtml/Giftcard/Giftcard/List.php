<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Giftcard_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'giftcard';
        $this->_controller = 'adminhtml_giftcard_giftcard_list';
        $this->_headerText = Mage::helper('giftcard')->__('Gift Cards');

        parent::__construct();
        $this->removeButton('add');
        return $this;
    }
}