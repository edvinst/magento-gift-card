<?php

class MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return  Mage::getModel('directory/currency')->format($row->getData($this->getColumn()->getIndex()), array('currency' => $row->getCurrency()), false);
    }
}