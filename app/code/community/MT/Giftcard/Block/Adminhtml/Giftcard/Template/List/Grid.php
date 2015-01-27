<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Template_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $__filters = array();

    private $__collectionEmpty = false;


    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcard_template_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (!$this->__collectionEmpty) {
            $collection = Mage::getResourceModel('giftcard/template_collection');
            $this->setCollection($collection);
        }

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('giftcard');
        $currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);

        $this->addColumn('entity_id', array(
            'header' => $helper->__(' #'),
            'index'  => 'entity_id',
            'width' => '60px',
        ));

        $this->addColumn('code', array(
            'header' => $helper->__('Name'),
            'index'  => 'name',
        ));

        $this->addColumn('design', array(
            'header' => $helper->__('Design'),
            'index'  => 'design',
            'width' => '200px',
        ));

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created At'),
            'type'   => 'datetime',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_DateTime',
            'index'  => 'created_at',
            'filter_index' => 'main_table.created_at',
            'width' => '160px',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function addCollectionFilter($field, $value)
    {
        $this->__filters[] = array(
            'field' => $field,
            'filter' => $value
        );
    }

    public function setCollectionIsEmpty($empty = true)
    {
        $this->__collectionEmpty = $empty;
    }

    public function applyCollectionFilters($collection)
    {
        if (count($this->__filters) == 0)
            return;

        foreach ($this->__filters as $filter) {
            $collection->addFieldToFilter($filter['field'], $filter['filter']);
        }
        return $collection;
    }

    public function getXlsFile()
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.xlsx';
        $a = $this->getColumns();

        $export = Mage::getModel('giftcard/export_grid');
        $export->setCollection($this->getCollection());
        $export->setAdapter(Mage::getModel('giftcard/export_adapter_excel'));
        $export->setGrid($this);
        $export->exportToFile($file);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => false // can delete file after use
        );
    }

}