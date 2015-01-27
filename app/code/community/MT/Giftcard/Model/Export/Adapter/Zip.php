<?php

class MT_Giftcard_Model_Export_Adapter_Zip implements MT_Giftcard_Model_Export_Adapter_Interface
{

    private $__zip = null;

    private $__files = array();

    public function __construct()
    {
        $this->__zip = new ZipArchive();

    }

    public function addFile($path, $fileName)
    {
        $this->__files[] = array(
            'path' => $path,
            'file_name' => $fileName
        );
    }

    public function getFiles()
    {
        return $this->__files;
    }

    public function getZip()
    {
        return $this->__zip;
    }

    public function addNext($giftCard)
    {
        $template = $giftCard->getTemplate();
        if (!$template)
            return false;

        $draw = Mage::getModel('giftcard/giftcard_draw');
        $draw->setGiftCard($giftCard);
        $draw->setTemplate($template);
        $draw->drawGiftCard();
        if (!$imagePath = $draw->getImagePath())
            return false;
        $this->addFile($imagePath, $giftCard->getId().'.jpg');
        return true;
    }

    public function saveToFile($fileName)
    {
        if ($this->getZip()->open($fileName, ZipArchive::CREATE) !== true)
            return false;

        $files = $this->getFiles();
        if (count($files) == 0)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('There is no data for export'));

        foreach ($files as $file) {

            $this->getZip()->addFile($file['path'], $file['file_name']);

        }
        $this->getZip()->close();

        foreach ($files as $file)
            unlink($file['path']);

        return file_exists($fileName);
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
}