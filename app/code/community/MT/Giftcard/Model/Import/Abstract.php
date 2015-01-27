<?php

//require_once('lib/PHPExcel.php');

abstract class MT_Giftcard_Model_Import_Abstract
{

    protected $_requiredFields = array();

    private  $__data = array();

    protected $__adapter = null;

    private $__filePath = '';

    protected $_imported = 0;

    protected $_skipped = 0;

    abstract protected function import($data);

    public function setFilePath($filePath)
    {
        $this->__filePath = $filePath;
    }

    public function getFilePath()
    {
        return $this->__filePath;
    }

    public function getFileExtension($filePath)
    {
        $tmp = explode('.',$filePath);
        if (count($tmp) == 0)
            return false;
        return strtolower($tmp[count($tmp)-1]);
    }

    public function getData()
    {
        return $this->__data;
    }

    public function importData($filePath)
    {
        $this->setFilePath($filePath);
        $ext = $this->getFileExtension($filePath);
        switch ($ext) {
            case 'csv';
                $this->setAdapter(Mage::getModel('giftcard/import_adapter_csv'));
                break;
            case 'xlsx';
                $this->setAdapter(Mage::getModel('giftcard/import_adapter_excel'));
                break;
        }
        if ($this->getAdapter() == null)
            throw new Exception(Mage::helper('giftcard')->__('Unable to read this file format.'));

        $this->prepareData();
        $this->import($this->getData());
    }

    public function prepareData()
    {
        $data = array();
        $this->getAdapter()->setFilePath($this->__filePath);
        $lineCount = $this->getAdapter()->getLineCount();
        $fields = $this->getAdapter()->getFields();

        if ($this->isValidFile()) {
            if ($lineCount > 1) {
                for ($i = 2; $i <= $lineCount; $i++) {
                    $row = array();
                    foreach ($fields as $field) {
                        $field = strtolower($field);
                        $row[$field] = $this->getAdapter()->getNext();
                    }
                    $this->getAdapter()->nextLine();
                    $data[] = $row;
                }
            }
        }
        $this->__data = $data;
    }

    public function setAdapter(MT_Giftcard_Model_Import_Adapter_Interface $adapter)
    {
        $this->__adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->__adapter;
    }

    public function getImportedItemCount()
    {
        return $this->_imported;
    }

    public function getSkippedItemCount()
    {
        return $this->_skipped;
    }

    public function getRequiredFields()
    {
        return $this->_requiredFields;
    }

    protected function isValidFile()
    {

        $requiredFields = $this->getRequiredFields();
        if (count($requiredFields) == 0)
            return true;

        $fileColumns = $this->getAdapter()->getFields();

        if ($requiredFields == $fileColumns)
            return true;

        $missedColumns = array();
        foreach ($requiredFields as $field) {
            if (!in_array($field, $fileColumns))
                $missedColumns[] = $field;
        }

        if (count($missedColumns) > 0) {
            throw new Exception(Mage::helper('giftcard')->__('Missed columns in file: %s', rtrim(implode(', ',$missedColumns)), ','));
        } else {
            throw new Exception(Mage::helper('giftcard')->__('Bad columns order in file'));
        }

    }

}