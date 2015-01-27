<?php

class MT_Giftcard_Model_Export_Adapter_Excel implements MT_Giftcard_Model_Export_Adapter_Interface
{
    protected $_sheet = 0;

    protected $_excel = null;

    protected  $_row = 0;

    protected $_column = -1;

    protected $_columnMarks = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function __construct()
    {
        $this->_excel = new PHPExcel();
    }

    public function addNext($data)
    {
        $this->_column++;
        $this->_excel->setActiveSheetIndex($this->_sheet)
            ->setCellValue($this->getCurrentCell(), $data);
    }

    public function newLine()
    {
        $this->_row++;
        $this->_column = -1;
    }

    public function setCurrentColumnWidth($width = 'auto')
    {
        if ($width == 'auto')
            $this->_excel->getActiveSheet()->getColumnDimension($this->getCurrentColumn())->setAutoSize(true);
        else
            $this->_excel->getActiveSheet()->getColumnDimension($this->getCurrentColumn())->setWidth($width);
    }


    public function setCurrentCellStyle($params)
    {
        if (isset($params['font-weight'])) {
            switch ($params['font-weight']) {
                case 'bold':
                    $this->_excel->getActiveSheet()->getStyle($this->getCurrentCell())->getFont()->setBold(true);
                    break;
            }
        }

    }

    public function getCurrentCell()
    {
        return $this->getCurrentColumn().$this->_row;
    }

    public function getCurrentColumn()
    {
        return $this->_columnMarks[$this->_column];
    }

    public function saveToFile($fileName)
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->_excel, 'Excel2007');
        $objWriter->save($fileName);
        return $objWriter->save($fileName);
    }

}