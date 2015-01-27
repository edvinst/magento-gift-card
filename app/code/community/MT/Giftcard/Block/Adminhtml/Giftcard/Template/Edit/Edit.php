<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Giftcard_Edit_Tabs_Edit extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {
        $helper = Mage::helper('giftcard');
        $data = array();
        if (Mage::registry('giftcard_data'))
            $data = Mage::registry('giftcard_data')->getData();

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general', array('legend' => Mage::helper('giftcard')->__('Gift Card Information')));

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
        ));

        $fieldset->addField('value', 'text', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Value'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'value',
        ));

        if(isset($data['entity_id']))
            $fieldset->addField('balance', 'text', array(
                'label' => Mage::helper('giftcard')->__('Current Balance'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'balance',
            ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Status'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'status',
            'values' => Mage::getModel('giftcard/giftcard_status')->toOptionArray()
        ));

        $fieldset->addField('currency', 'select', array(
            'label' => Mage::helper('giftcard')->__('Currency'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'currency',
            'values' => Mage::getModel('giftcard/system_config_source_locale_currency')->toOptionArray()
        ));

        $fieldset->addField('available_days', 'text', array(
            'name' => 'available_days',
            'label' => Mage::helper('giftcard')->__('Active (Days)'),
            'required' => false,
            'after_element_html' => $helper->__('note_after_available_days'),
        ));

        $timeFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('expired_at', 'date', array(
            'name' => 'expired_at',
            'label' => Mage::helper('giftcard')->__('Expired At'),
            'required' => false,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'=> $timeFormat,
            'input_format'=> $timeFormat,
            'after_element_html' => $helper->__('note_after_expired_at'),
            'time' => true
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }
}