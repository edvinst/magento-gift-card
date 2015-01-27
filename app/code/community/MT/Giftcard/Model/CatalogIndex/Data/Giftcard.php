<?php

class MT_Giftcard_Model_CatalogIndex_Data_Giftcard extends Mage_CatalogIndex_Model_Data_Abstract
{
    public function getTypeCode()
    {
        return MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT;
    }
}
