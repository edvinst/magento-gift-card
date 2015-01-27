<?php

class MT_Giftcard_Block_Adminhtml_Sales_Creditmemo_View_Total_Giftcard
    extends Mage_Core_Block_Template
{
    protected $_giftCardArray = null;

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $parent->removeTotal('mt_gift_card_total');

        $creditMemo = $parent->getCreditmemo();
        $giftCards = $creditMemo->getMtGiftCard();
        if (!$giftCards)
            return $this;

        $giftCards = unserialize($giftCards);

        foreach ($giftCards as $key => $giftCard) {
            if (!isset($giftCard['refunded']))
                continue;

            $total = new Varien_Object(array(
                'code'      => 'mt_gift_card_total_'.$key,
                'value' => $giftCard['refunded'],
                'base_value' => $giftCard['refunded'],
                'label'     => $this->__('Refund to Gift Card (%s)', $giftCard['code']),
            ));

            $parent->addTotal($total);
        }

        return $this;
    }

}