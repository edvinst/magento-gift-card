<?php

//require_once('lib/PHPExcel.php');

interface MT_Giftcard_Model_Export_Adapter_Interface
{
    public function addNext($data);

    public function newLine();

    public function saveToFile($fileName);

    public function setCurrentColumnWidth($width = 'auto');

    public function setCurrentCellStyle($params);

}