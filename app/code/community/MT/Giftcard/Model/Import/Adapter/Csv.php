<?php

class MT_Giftcard_Model_Import_Adapter_Csv extends MT_Giftcard_Model_Import_Adapter_Excel
{
    public function init()
    {
        $objReader = PHPExcel_IOFactory::createReader('CSV');
        $this->_reader = $objReader->load($this->_filePath);
        $this->_data = $this->_reader->getActiveSheet()->toArray(null,true,true,true);
    }

}