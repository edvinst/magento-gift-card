<?php

class MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_DateTime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
        $date = $row->getData($this->getColumn()->getIndex());
        if ($date == '')
            return '-';

        return parent::render($row);
    }
}