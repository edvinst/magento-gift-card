<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Series_Edit_Tabs_Edit extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {
        $helper = Mage::helper('giftcard');
        $data = array();
        if (Mage::registry('giftcard_series_data'))
            $data = Mage::registry('giftcard_series_data')->getData();

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general', array('legend' => Mage::helper('giftcard')->__('Series Information')));
        $fieldset2 = $form->addFieldset('defaults', array('legend' => Mage::helper('giftcard')->__('Gift Card Generator Settings')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('giftcard')->__('Series Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'series[name]',
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('giftcard')->__('Series Description'),
            'required' => false,
            'name' => 'series[description]',
        ));

        $fieldset->addField('value', 'text', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Value'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'series[value]',
        ));

        $fieldset->addField('lifetime', 'text', array(
            'name' => 'series[lifetime]',
            'label' => Mage::helper('giftcard')->__('Lifetime (days)'),
            'required' => false,
            'after_element_html' => '<br/><small>'.$helper->__('0 - no expired').'</small>',
        ));

        $fieldset->addField('template_id', 'select', array(
            'label' => Mage::helper('giftcard')->__('Gift Card Template'),
            'name' => 'series[template_id]',
            'values' => Mage::getModel('giftcard/adminhtml_system_config_source_giftcard_template')->toOptionArray()
        ));


        /* generator defaults */

        $fieldset2->addField('default_length', 'text', array(
            'name'     => 'series[default_length]',
            'label'    => $helper->__('Code Length'),
            'required' => true,
            'note'     => $helper->__('Excluding prefix, suffix and separators.'),
            'value'    => 12,
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $fieldset2->addField('default_format', 'select', array(
            'label'    => $helper->__('Code Format'),
            'name'     => 'series[default_format]',
            'values'  => Mage::getModel('giftcard/adminhtml_system_config_source_giftcard_format')->toOptionArray(),
            'required' => true,
        ));

        $fieldset2->addField('default_prefix', 'text', array(
            'name'  => 'series[default_prefix]',
            'label' => $helper->__('Code Prefix'),
        ));

        $fieldset2->addField('default_suffix', 'text', array(
            'name'  => 'series[default_suffix]',
            'label' => $helper->__('Code Suffix'),
        ));

        $fieldset2->addField('default_dash', 'text', array(
            'name'  => 'series[default_dash]',
            'label' => $helper->__('Dash Every X Characters'),
            'note'  => $helper->__('If empty no separation.'),
            'value' => 0,
            'class' => 'validate-digits'
        ));



        $form->setValues($data);

        return parent::_prepareForm();
    }
}