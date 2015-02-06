<?php

class MT_Giftcard_Block_Sales_Order_Info_Buttons
    extends Mage_Core_Block_Template
{
    public function getGiftCardPdf()
    {
        $order = $this->getOrder();
        $items = $order->getAllItems();

        if (count($items) != 0) {
            foreach ($items as $item) {
                if ($item->getProductType()== MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT) {
                    return Mage::getUrl('giftcard/giftcard/pdf', array('id' => $this->getOrder()->getId()));
                }
            }
        }

        return false;
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }
}