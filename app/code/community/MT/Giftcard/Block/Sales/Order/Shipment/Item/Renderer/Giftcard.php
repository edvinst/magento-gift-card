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