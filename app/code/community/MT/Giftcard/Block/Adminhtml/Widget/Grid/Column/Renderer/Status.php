<?php

class MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return  Mage::helper('giftcard')->__($row->getData($this->getColumn()->getIndex()));
    }
}