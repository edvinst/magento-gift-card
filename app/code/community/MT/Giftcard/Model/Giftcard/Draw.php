<?php


class MT_Giftcard_Model_Giftcard_Draw
{
    private $__template = null;

    private $__giftCard = null;

    private $__design = null;

    public function setTemplate(MT_Giftcard_Model_Template $template)
    {
        $this->__template = $template;
    }

    public function setGiftCard(MT_Giftcard_Model_Giftcard $giftCard)
    {
        $this->__giftCard = $giftCard;
    }

    public function getTemplate()
    {
        return $this->__template;
    }

    public function getGiftCard()
    {
        return $this->__giftCard;
    }

    public function getDesign()
    {
        if ($this->__design == null && $this->getGiftCard()->getTemplate()->getDesign() != '') {
            $this->__design = Mage::getModel('giftcard/giftcard_design_'.$this->getGiftCard()->getTemplate()->getDesign());
        }
        return $this->__design;
    }

    public function drawGiftCard()
    {
        $giftCard = $this->getGiftCard();
        if ($this->getGiftCard() == null)
            return false;

        $this->getDesign()->setGiftCard($giftCard);
        $this->getDesign()->draw();
    }

    public function getImageSource()
    {
        return $this->getDesign()->getImageSource();
    }

    public function getImagePath()
    {
        return $this->getDesign()->getImagePath();
    }

}