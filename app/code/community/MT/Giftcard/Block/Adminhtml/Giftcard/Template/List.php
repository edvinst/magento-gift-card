<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Template_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'giftcard';
        $this->_controller = 'adminhtml_giftcard_template_list';
        $this->_headerText = Mage::helper('giftcard')->__('Gift Card Template');
        parent::__construct();
    }

}