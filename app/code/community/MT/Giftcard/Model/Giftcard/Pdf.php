<?php

class MT_Giftcard_Model_Giftcard_Pdf
{
    private $__order = null;

    private $__giftCard = null;

    private $__pages = array();

    private $__pdf = null;

    private $__pdfPath = '';

    private $__pdfName = '';


    public function __construct()
    {
        $this->__pdf = new Zend_Pdf();

    }

    public function getPdfPath()
    {
        return $this->__pdfPath;
    }

    public function getPages()
    {
        return $this->__pages;
    }

    public function setOrder($order)
    {
        $this->__order = $order;
    }

    public function setGiftCard($giftCard)
    {
        $this->__giftCard = $giftCard;
    }

    public function getGiftCard()
    {
        return $this->__giftCard;
    }

    public function getPdf()
    {
        return $this->__pdf;
    }

    public function addPage($page)
    {
        $this->getPdf()->pages[] = $page;
    }

    public function getOrder()
    {
        return $this->__order;
    }


    public function createGiftCardPdf()
    {
        if (!$giftCard = $this->getGiftCard())
            throw new Exception('Set gift card first');

        $fileName = md5('gift_card_' . $giftCard->getId()) . '.pdf';
        $filePath = Mage::getBaseDir('tmp') . DS . $fileName;
        $this->addGiftCard($this->getGiftCard());
        $this->getPdf()->save($filePath, true);

        //unlink($imagePath);
        $this->__pdfPath = $filePath;
        $this->__pdfName = $fileName;
        return true;
    }

    public function createPdf()
    {
        if (!$order = $this->getOrder())
            throw new Exception('Set order first');

        //cache files
        //       if (file_exists($filePath))
        //          return $filePath;

        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('order_id', $order->getId());
        if ($giftCardCollection->count() == 0)
            return false;

        $fileName = md5($order->getIncrementId()) . '.pdf';
        $filePath = Mage::getBaseDir('tmp') . DS . $fileName;

        foreach ($giftCardCollection as $giftCard) {
            $this->addGiftCard($giftCard);
        }

        $this->getPdf()->save($filePath, true);

        //TODO delete tmp
        //unlink($imagePath);
        $this->__pdfPath = $filePath;
        $this->__pdfName = $fileName;
        return true;
    }

    public function addGiftCard($giftCard)
    {
        $template = $giftCard->getTemplate();
        if (!$template)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Please set template for gift card'));

        $draw = Mage::getModel('giftcard/giftcard_draw');
        $draw->setGiftCard($giftCard);
        $draw->setTemplate($template);
        $draw->drawGiftCard();

        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        if(!$imagePath = $draw->getImagePath())
            return false;

        $image = Zend_Pdf_Image::imageWithPath($imagePath);

        $imgWidthPts = $image->getPixelWidth() * 72 / 96;
        $imgHeightPts = $image->getPixelHeight() * 72 / 96;

        $rate = $imgWidthPts / $page->getWidth();
        $imgWidthPts = $imgWidthPts / $rate;
        $imgHeightPts = $imgHeightPts / $rate;
        $pageHeight = $page->getHeight();

        $page->drawImage($image, 0, $pageHeight - $imgHeightPts, $imgWidthPts, $pageHeight);
        $this->addPage($page);

        unlink($imagePath);
        return true;
    }


}