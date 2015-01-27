<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Series_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('giftcard_series_tab');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('giftcard')->__('Gift Card Series Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('general', array(
            'label' => Mage::helper('giftcard')->__('Series Information'),
            'title' => Mage::helper('giftcard')->__('Series Information'),
            'content' => $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_edit_tabs_edit')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}