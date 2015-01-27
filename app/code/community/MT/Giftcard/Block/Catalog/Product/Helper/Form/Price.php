<?php

class MT_Giftcard_Block_Catalog_Product_Helper_Form_Price extends Varien_Data_Form_Element_Text
{

    public function getDefaultHtml()
    {

            $html = ( $this->getNoSpan() === true ) ? '' : '<span class="field-row">'."\n";
            $html.= $this->getLabelHtml();
            $html.= $this->getElementHtml();
            $html.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";

        return $html;

    }

    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        return $html."  <script>$('".$this->getHtmlId()."').disable();</script>";
    }
}