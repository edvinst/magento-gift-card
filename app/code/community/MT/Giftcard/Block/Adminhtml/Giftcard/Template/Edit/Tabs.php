<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Giftcard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('giftcard_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftcard')->__('Gift Card Template Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('general', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Template Information'),
            'title' => Mage::helper('giftcard')->__('Gift Card Template Information'),
            'content' => $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_edit_tabs_edit')->toHtml(),
        ));


        return parent::_beforeToHtml();
    }
}