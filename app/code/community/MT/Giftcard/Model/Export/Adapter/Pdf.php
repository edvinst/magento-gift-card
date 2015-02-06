<?php

class MT_Giftcard_Model_Export_Adapter_Pdf implements MT_Giftcard_Model_Export_Adapter_Interface
{

    private $__pages = array();

    private $__pdf = null;

    public function __construct()
    {
        $this->__pdf = new Zend_Pdf();
    }

    public function addPage($page)
    {
        $this->getPdf()->pages[] = $page;
    }

    public function getPdf()
    {
        return $this->__pdf;
    }

    public function getPages()
    {
        return $this->__pages;
    }

    public function addNext($giftCard)
    {
        $template = $giftCard->getTemplate();
        if (!$template)
            return false;

        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $draw = Mage::getModel('giftcard/giftcard_draw');
        $draw->setGiftCard($giftCard);
        $draw->setTemplate($template);
        $draw->drawGiftCard();
        if (!$imagePath = $draw->getImagePath())
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

    public function newLine()
    {

    }

    public function setCurrentColumnWidth($width = 'auto')
    {

    }

    public function setCurrentCellStyle($params)
    {

    }

    public function saveToFile($fileName)
    {
        if (count($this->getPdf()->pages) == 0)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('There is no data for export'));

        $this->getPdf()->save($fileName);
    }
}