<?php

class MT_Giftcard_Model_Sales_Order_Creditmemo_Total_Giftcard
    extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $order = $creditmemo->getOrder();
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $order->getMtGiftCardTotal());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $order->getBaseMtGiftCardTotal());
        return $this;
    }
}
