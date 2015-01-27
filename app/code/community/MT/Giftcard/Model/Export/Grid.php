<?php

//require_once('lib/PHPExcel.php');

class MT_Giftcard_Model_Export_Grid extends MT_Giftcard_Model_Export_Abstract
{
    protected $_grid = null;

    public function setGrid(Mage_Adminhtml_Block_Widget_Grid $grid)
    {
        $this->_grid = $grid;
    }

    public function getGrid()
    {
        return $this->_grid;
    }

    public function exportItem($item)
    {
        $grid = $this->getGrid();
        $columns = $grid->getColumns();
        foreach ($columns as $column) {
            if ($this->isExportColumn($column)) {
                $this->exportItemField($column->getRowFieldExport($item));
            }
        }
    }

    protected function exportHeadline()
    {
        $grid = $this->getGrid();
        $columns = $grid->getColumns();
        $this->getAdapter()->newLine();
        foreach ($columns as $column) {
            if ($this->isExportColumn($column)) {
                $this->exportHeadlineField($column->getHeader());
                $this->getAdapter()->setCurrentCellStyle(array('font-weight' => 'bold'));
            }
        }
    }

    protected function isExportColumn($column)
    {
        return !$column->getIsSystem();
    }

}