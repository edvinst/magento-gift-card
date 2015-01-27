<?php

class MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Empty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return ($row->getData($this->getColumn()->getIndex()) != '')?$row->getData($this->getColumn()->getIndex()):'-';
    }
}