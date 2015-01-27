<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm()
    {
        $helper = Mage::helper('giftcard');
        $data = array();
        if (Mage::registry('gift_card_template_data'))
            $data = Mage::registry('gift_card_template_data')->getData();

       // $form = new Varien_Data_Form();
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('gift_card_template', array('legend' => Mage::helper('giftcard')->__('Gift Card Template Information')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('giftcard')->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'general[name]',
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('giftcard')->__('Description'),
            'name' => 'general[description]',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('giftcard')->__('Is Active'),
            'class' => 'required-entry',
            'name' => 'general[is_active]',
            'values' => Mage::getSingleton('eav/entity_attribute_source_boolean')->getOptionArray()
        ));



        $designArray = array_merge(array('' => ''), Mage::getSingleton('giftcard/adminhtml_system_config_source_giftcard_design')->toOptionArray());
        $fieldset->addField('design', 'select', array(
            'name' => 'general[design]',
            'label' => Mage::helper('giftcard')->__('Design'),
            'required' => true,
            'class' => 'required-entry',
            'values' => $designArray,
        ));



        if (Mage::app()->getRequest()->getParam('id') > 0) {
            $data['template_id'] = $data['entity_id'];
            $fieldset->addField('template_id', 'hidden', array(
                'name' => 'template_id',
                'class' => 'gift_card_design_value',
                'index' => 'template_id'
            ));

            $designFieldset = array();
            foreach ($designArray as $key => $design) {
                if (!isset($design['value']))
                    continue;

                $designName = $design['value'];
                $additionalThemeFields = Mage::getModel('giftcard/giftcard_design_'.$designName)->getAdditionalFields();

                if (count($additionalThemeFields) > 0) {
                    $designFieldset[$designName] = $form->addFieldset('gift_card_design_'.$designName, array(
                        'legend' => Mage::helper('giftcard')->__('Design Options'),
                        'class' => 'gift_card_design_fieldset'
                    ));

                    foreach ($additionalThemeFields as $field) {
                        if (!isset($field[2]['name']))
                            throw new Exception('Design field "'.$field[0].'" must have name option');

                        //add design prefix for field
                        $designFieldset[$designName]->addField($field[0], $field[1], $field[2]);

                        //set design data
                        if (isset($data[$field[2]['index']]))
                            $data[$field[0]]= $data[$field[2]['index']];
                    }
                }
            }

        }

        if ($data)
            $form->setValues($data);

        return parent::_prepareForm();
    }

}

/*
 protected function _prepareForm() {

     if (Mage::registry('giftcard_data')) {
         $data = Mage::registry('giftcard_data')->getData();
     } else {
         $data = array();
     }

     $form = new Varien_Data_Form(array(
             'id' => 'edit_form',*/
            // 'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
          /*      'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($data);
        return parent::_prepareForm();
    }*/