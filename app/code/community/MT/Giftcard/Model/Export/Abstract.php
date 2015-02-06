<?php

//require_once('lib/PHPExcel.php');

abstract class MT_Giftcard_Model_Export_Abstract
{
    protected  $_collection = null;

    private $__adapter = null;

    abstract protected function exportHeadline();

    abstract protected  function exportItem($item);

    public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    public function setAdapter(MT_Giftcard_Model_Export_Adapter_Interface $adapter)
    {
        $this->__adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->__adapter;
    }

    protected  function iterateCollection()
    {
        $collection = $this->getCollection();

        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $this->getAdapter()->newLine();
                $this->exportItem($item);
            }
        }
    }

    protected function exportItemField($value)
    {
        $this->getAdapter()->addNext(strip_tags($value));
    }

    protected function exportHeadlineField($value, $width = false)
    {
        $value = Mage::helper('giftcard')->__(ucfirst($value));
        $this->getAdapter()->addNext($value);
        if (is_numeric($width))
            $this->getAdapter()->setCurrentColumnWidth($width);
    }

    public function exportToFile($fileName)
    {
        $this->exportHeadline();
        $this->iterateCollection();
        $this->getAdapter()->saveToFile($fileName);
    }
}