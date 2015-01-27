<?php

class MT_Giftcard_Model_Catalog_Product_Option_Type_GiftcardValue
    extends Mage_Catalog_Model_Product_Option_Type_Default
{
    public function isCustomizedView()
    {
        return true;
    }

    public function getCustomizedView($optionInfo)
    {
        $customizeBlock = new MT_Giftcard_Block_Catalog_Product_Options_Type_Customview_GiftcardValue();
        $customizeBlock->setInfo($optionInfo);
        return $customizeBlock->toHtml();
    }
}