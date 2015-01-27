<?php

class MT_Giftcard_Block_Sales_Order_Shipment_Item_Renderer_Giftcard
    extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $giftCardValue = '';
        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItemId());

        if ($giftCardCollection->count() > 0) {
            foreach ($giftCardCollection as $giftCard) {
                $giftCardValue.=$giftCard->getCode().'<br/>';
            }

            $options = array(
                array(
                    'label' => Mage::helper('giftcard')->__('Gift Card'),
                    'value' => $giftCardValue
            ));

            $result = array_merge($result, $options);
        }


        return $result;
    }
}




/*


        if ($product->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT) {
            $giftCardValue = '';
            $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
                ->addFieldToFilter('order_item_id', $this->getItem()->getId());

            if ($giftCardCollection->count() > 0) {
                foreach ($giftCardCollection as $giftCard) {
                    $giftCardValue.=$giftCard->getCode().'('.Mage::helper('giftcard')->__($giftCard->getStatus()).')<br/>';
                }
                $productOptions = $this->getItem()->getProductOptions();
                $options = array(
                    array(
                        'label' => Mage::helper('giftcard')->__('Gift Card'),
                        'value' => $giftCardValue
                    ),
                    array(
                        'label' => Mage::helper('giftcard')->__('Gift Card Type'),
                        'value' => Mage::helper('giftcard')->__(Mage::getResourceModel('catalog/product')->getAttributeRawValue($this->getItem()->getProductId(), 'gift_card_type', Mage::app()->getStore()->getId()))
                    ),
                    array(
                        'label' => Mage::helper('giftcard')->__('Physical Gift Card'),
                        'value' => (isset($productOptions['info_buyRequest']['giftcard_attribute']['giftcard_is_real'])
                            && $productOptions['info_buyRequest']['giftcard_attribute']['giftcard_is_real'] = 1
                            )?Mage::helper('giftcard')->__('Yes'):Mage::helper('giftcard')->__('No')
                    ),
                );

                $result = array_merge($result, $options);
            }
        }
 */