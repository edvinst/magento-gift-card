<?php

class MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Days extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $__series = array();

    public function render(Varien_Object $row)
    {
        $days = $row->getData($this->getColumn()->getIndex());
        if (!is_numeric($days) )
            return '-';

        $helper = Mage::helper('giftcard');
        if ($days == 0)
            return '-';
        return $days==1?$days.$helper->__('day'):$days.$helper->__('days');
    }
}