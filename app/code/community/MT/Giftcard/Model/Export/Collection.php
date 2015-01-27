<?php

//require_once('lib/PHPExcel.php');

class MT_Giftcard_Model_Export_Collection extends MT_Giftcard_Model_Export_Abstract
{
    protected $_fields = null;

    public function exportItem($item)
    {
        $fields = $this->getFields();
        foreach ($fields as $field)
            $this->exportItemField($item->getData($field));
    }

    protected function exportHeadline()
    {
        $fields = $this->getFields();
        $this->getAdapter()->newLine();
        foreach ($fields as $field) {
            $this->exportHeadlineField($field);
            $this->getAdapter()->setCurrentCellStyle(array('font-weight' => 'bold'));
        }
    }

    public  function getFields()
    {
        if ($this->_fields == null) {
            $firstItem = $this->getCollection()->getFirstItem()->getData();
            foreach ($firstItem as $column => $value)
                $this->_fields[] = $column;
        }

        return $this->_fields;
    }



}