<?php

class MT_Giftcard_Model_Export_Adapter_Csv extends MT_Giftcard_Model_Export_Adapter_Excel
{
    public function saveToFile($fileName)
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->_excel, 'CSV');
        $objWriter->save($fileName);
        return $objWriter->save($fileName);
    }

}