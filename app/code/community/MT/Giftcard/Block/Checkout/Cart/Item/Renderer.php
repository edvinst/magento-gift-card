<?php

class MT_Giftcard_Block_Checkout_Cart_Item_Renderer
    extends Mage_Checkout_Block_Cart_Item_Renderer
{
    public function getProductOptions()
    {
        $parent = parent::getProductOptions();
        if (!Mage::helper('giftcard')->isActive() || $this->getProduct()->getTypeId() != MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
            return $parent;

        $giftCardHelper = Mage::helper('giftcard/catalog_product_configuration');
        $giftCardOptions = $giftCardHelper->getCustomOptions($this->getItem());

        return array_merge($giftCardOptions, $parent);
    }
}