<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        parent::__construct();
        $helper = Mage::helper('giftcard');
        $this->_objectId = 'id';
        $this->_blockGroup = 'giftcard';
        $this->_controller = 'adminhtml_giftcard_template';
        $this->_mode = 'edit';
        $this->_updateButton('save', 'label', $helper->__('Save'));
        $this->_updateButton('delete', 'label', $helper->__('Delete'));
        $this->_addButton('saveandcontinue', array(
            'label' => $helper->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);
        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
    }

    public function getHeaderText()
    {
        $series = Mage::registry('gift_card_template_data');
        if ($series && $series->getId()) {
            return Mage::helper('giftcard')->__('Edit Gift Card Template "%s"', $this->htmlEscape($series->getCode()));
        } else {
            return Mage::helper('giftcard')->__('New Gift Card Template');
        }
    }
}