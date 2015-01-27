<?php

class MT_Giftcard_Block_Adminhtml_Sales_Creditmemo_Create_Total_Giftcard
    extends Mage_Core_Block_Template
{
    protected $_giftCardArray = null;

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $giftCards = $this->getGiftCardArray();
        if (!$giftCards)
            return $this;

        $total = new Varien_Object(array(
            'code'      => 'mt_gift_card_total',
            'block_name' => 'gift_card_total'
        ));

        $parent->addTotal($total);
        return $this;
    }


    public function getGiftCardArray()
    {
        if ($this->_giftCardArray == null) {
            $parent = $this->getParentBlock();
            $order = $parent->getOrder();
            $giftCards = $order->getMtGiftCard();
            if (empty($giftCards))
                return false;

            $giftCards = unserialize($giftCards);

            if (!$giftCards)
                return false;

            foreach ($giftCards as $key => $giftCard) {
                if (!isset($giftCard['code'])) {
                    unset($giftCard[$key]);
                    continue;
                }

                $model = Mage::getModel('giftcard/giftcard')->loadByCode($giftCard['code']);
                if (!is_numeric($model->getId())) {
                    unset($giftCards[$key]);
                    continue;
                }


                $giftCards[$key]['source'] = $model;
                $giftCards[$key]['total'] = 0;

                if (isset($giftCard['discount']))
                    $giftCards[$key]['total'] = $giftCard['discount']*-1;

                if (isset($giftCard['refunded']))
                    $giftCards[$key]['total']-=$giftCard['refunded'];

                if ($giftCards[$key]['total'] < 0)
                    $giftCards[$key]['total'] = 0;

                $giftCards[$key]['total'] = Mage::getModel('directory/currency')->format(
                    $giftCards[$key]['total'],
                    array('display'=>Zend_Currency::NO_SYMBOL),
                    false
                );
            }
            $this->_giftCardArray = $giftCards;
        }

        return $this->_giftCardArray;
    }
}