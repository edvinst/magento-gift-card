<?php

class MT_Giftcard_Block_Adminhtml_Sales_Items_Column_Name_Giftcard
    extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
    public function getOrderOptions()
    {
        $result = parent::getOrderOptions();

        if ($this->getItem()->getProductType() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT) {
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
                );

                $result = array_merge($result, $options);
            }
        }

        return $result;
    }
}