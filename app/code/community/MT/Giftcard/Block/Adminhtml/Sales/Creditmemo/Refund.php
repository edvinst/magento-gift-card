<?php

class MT_Giftcard_Block_Adminhtml_Sales_Creditmemo_Refund
    extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::_construct();
        $this->setTemplate('mt/giftcard/sales/creditmemo/refund.phtml');
        return $this;
    }

    public function getGiftCardRefundTotal()
    {
        $creditMemo =  Mage::registry('current_creditmemo');
        $order = $creditMemo->getOrder();

        $refundedTotal = $collection = Mage::getModel('sales/order_creditmemo')->getCollection()
            ->addAttributeToFilter('order_id', $order->getId())
            ->addExpressionFieldToSelect('total', 'SUM({{mt_gift_card_refund}})', 'mt_gift_card_refund')
            ->setPageSize(1);
        $refunded = $refundedTotal->getFirstItem()->getData('total');

        $total = ($order->getMtGiftCardTotal()*-1);

        if($refunded!=0)
            $total-=$refunded;

        if ($total <= 0)
            return 0;

        $formatedTotal = Mage::getModel('directory/currency')->format(
            $total,
            array('display'=>Zend_Currency::NO_SYMBOL),
            false
        );

        return $formatedTotal;

    }


}