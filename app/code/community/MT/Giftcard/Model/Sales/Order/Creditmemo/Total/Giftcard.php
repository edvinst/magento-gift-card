<?php

class MT_Giftcard_Model_Sales_Order_Creditmemo_Total_Giftcard
    extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{


    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $order = $creditmemo->getOrder();
        $giftCardRefund = $order->getMtGiftCardTotal();
        $baseGiftCardRefund = $order->getBaseMtGiftCardTotal();

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftCardRefund);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseGiftCardRefund);
        return $this;
    }
}
