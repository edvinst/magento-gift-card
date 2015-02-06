<?php

class MT_Giftcard_Block_Sales_Order_Print_Item_Renderer_Giftcard
    extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $product = $this->getItem()->getProduct();
        if ($product->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT) {
            $giftCardValue = '';
            $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
                ->addFieldToFilter('order_item_id', $this->getItem()->getId());

            if ($giftCardCollection->count() > 0) {
                foreach ($giftCardCollection as $giftCard) {
                    $giftCardValue.=$giftCard->getCode().' ('.Mage::helper('giftcard')->__($giftCard->getStatus()).')'."\n";
                }

                $options = array(
                    array(
                        'label' => Mage::helper('giftcard')->__('Gift Card'),
                        'value' => $giftCardValue
                ));


                $result = array_merge($result, $options);
            }
        }
        return $result;
    }
}