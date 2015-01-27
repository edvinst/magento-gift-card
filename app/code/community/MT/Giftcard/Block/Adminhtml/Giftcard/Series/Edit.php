<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Series_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        parent::__construct();
        $helper = Mage::helper('giftcard');
        $this->_objectId = 'id';
        $this->_blockGroup = 'giftcard';
        $this->_controller = 'adminhtml_giftcard_series';
        $this->_mode = 'edit';
        $this->_updateButton('save', 'label', $helper->__('Save'));
        $this->_updateButton('delete', 'label', $helper->__('Delete'));
        $objId = $this->getRequest()->getParam($this->_objectId);
        if (! empty($objId)) {
            $this->_addButton('deleteall', array(
                'label'     => $helper->__('Delete With Gift Cards'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. $helper->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getDeleteAllUrl() . '\')',
            ));
        }
        $this->_addButton('saveandcontinue', array(
            'label' => $helper->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);
        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
    }

    public function getDeleteAllUrl()
    {
        $objId = $this->getRequest()->getParam($this->_objectId);
        return Mage::helper("adminhtml")->getUrl('*/*/deleteall', array('id' => $objId));
    }

    public function getHeaderText()
    {
        $series = Mage::registry('giftcard_series_data');
        if ($series && $series->getId()) {
            return Mage::helper('giftcard')->__('Edit Gift Card Series "%s"', $this->htmlEscape($series->getName()));
        } else {
            return Mage::helper('giftcard')->__('New Gift Card Series');
        }
    }
}