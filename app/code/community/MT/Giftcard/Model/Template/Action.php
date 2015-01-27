<?php

class MT_Giftcard_Model_Template_Action
{

    public function giftCardsTemplateDelete($giftCardTemplateIds)
    {
        if (count($giftCardTemplateIds) == 0)
            return;

        foreach ($giftCardTemplateIds as $id)
            $this->giftCardTemplateDelete($id);
    }

    public function giftCardTemplateDelete($id)
    {
        $giftCard = Mage::getModel('giftcard/template')->load($id);
        $giftCard->delete();
    }
}