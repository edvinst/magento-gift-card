<?php

//require_once('lib/PHPExcel.php');

interface MT_Giftcard_Model_Import_Adapter_Interface
{

    public function setFilePath($filePath);

    public function getLineCount();

    public function getNext();

    public function nextLine();

    public function getFields();



}