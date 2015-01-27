<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Template_Edit_Theme_One
{
    protected function getFieldArray()
    {
        $helper = Mage::helper('giftcard');
        $fields = array();

        $fields[] = array('title', 'text', array(
            'label' => $helper->__('Title'),
            'class' => 'required-entry',
            'name' => 'title',
        ));

        $fields[] = array('title_size', 'text', array(
            'label' => $helper->__('Title Size'),
            'required' => true,
            'name' => 'title_size',
        ));

        $fields[] = array('title_y', 'text', array(
            'label' => $helper->__('Title Y'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title_y',
        ));

        $fields[] = array('title2', 'text', array(
            'label' => $helper->__('Logo Text'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title2',
        ));


        $fields[] = array('note', 'textarea', array(
            'label' => $helper->__('Note'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'note',
        ));


        return $fields;
    }

}