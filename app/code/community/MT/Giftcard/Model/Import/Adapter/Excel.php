<?php

class MT_Giftcard_Model_Import_Adapter_Excel implements MT_Giftcard_Model_Import_Adapter_Interface
{
    protected  $_filePath;

    protected $_data = null;

    protected $_fields = null;

    protected $_sheet = 0;

    protected $_reader = null;

    protected  $_row = 1;

    protected $_column = -1;

    protected $_columnMarks = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function __construct()
    {

    }

    public function init()
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $this->_reader = $objReader->load($this->_filePath);
        $this->_data = $this->_reader->getActiveSheet()->toArray(null,true,true,true);
    }

    public function setFilePath($filePath)
    {
        $this->_filePath = $filePath;
    }



    public function getFields()
    {

        if ($this->_fields == null) {
            $this->_fields = array();
            if ($this->_reader == null)
                $this->init();
            $fieldsTmp = $this->_data[1];

            if ($fieldsTmp) {
                foreach ($fieldsTmp as $fieldName) {
                    $this->_fields[] = $fieldName;
                }
            }
            $this->nextLine();
        }

        return $this->_fields;
    }

    public function getLineCount()
    {
        if ($this->_reader == null)
            $this->init();

        return count($this->_data);
    }

    public function getNext()
    {   $this->_column++;
        $string = '';
        if (isset($this->_data[$this->_row][$this->_columnMarks[$this->_column]]))
            $string = $this->_data[$this->_row][$this->_columnMarks[$this->_column]];
        return $string;
    }

    public function nextLine()
    {
        $this->_row++;
        $this->_column = -1;
    }

}