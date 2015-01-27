<?php

class MT_Giftcard_Model_Sales_Order_Invoice_Total_Giftcard
    extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        parent::collect($invoice);
        $order = $invoice->getOrder();

        $invoice->setGrandTotal($invoice->getGrandTotal() + $order->getMtGiftCardTotal());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $order->getBaseMtGiftCardTotal());

        return $this;
    }
}
