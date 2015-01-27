<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Giftcard_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $__filters = array();

    private $__collectionEmpty = false;


    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcard_giftcard_list_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (!$this->__collectionEmpty) {
            $collection = Mage::getResourceModel('giftcard/giftcard_collection');
            $collection->getSelect()
                ->joinLeft(array('t1' => 'mt_giftcard_series'), 'main_table.series_id=t1.entity_id', array('series_name' => 't1.name'));
            $this->applyCollectionFilters($collection);
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
            'filter_index' => 'main_table.entity_id',
            'width' => '60px',
        ));

        $this->addColumn('code', array(
            'header' => $helper->__('Code'),
            'index'  => 'code',
            'filter_index' => 'main_table.code',
        ));

        $this->addColumn('value', array(
            'header' => $helper->__('Initial Value'),
            'index'  => 'value',
            'filter_index' => 'main_table.value',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Price',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'width' => '100px',
        ));

        $this->addColumn('balance', array(
            'header' => $helper->__('Current Balance'),
            'index'  => 'balance',
            'filter_index' => 'main_table.balance',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Price',
            'width' => '100px',
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index'  => 'status',
            'filter_index' => 'main_table.status',
            'width' => '80px',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Status',
            'type'      => 'options',
            'options'   => Mage::getModel('giftcard/adminhtml_system_config_source_giftcard_status')->toKeyValueArray()
        ));

        $this->addColumn('series_name', array(
            'header' => $helper->__('Series'),
            'index'  => 'series_name',
            'filter_index' => 't1.name',
            'width' => '100px',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Empty',
        ));

        $this->addColumn('lifetime', array(
            'header' => $helper->__('Lifetime'),
            'index'  => 'lifetime',
            'filter_index' => 'main_table.lifetime',
            'width' => '80px',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Days',
        ));

        $this->addColumn('expired_at', array(
            'header' => $helper->__('Expired At'),
            'index'  => 'expired_at',
            'filter_index' => 'main_table.expired_at',
            'type'   => 'datetime',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_DateTime',
            'width' => '160px',
        ));

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created At'),
            'type'   => 'datetime',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_DateTime',
            'index'  => 'created_at',
            'filter_index' => 'main_table.created_at',
            'width' => '160px',
        ));

        $this->addExport();

        $this->addColumn('state', array(
            'header' => $helper->__('State'),
            'index'  => 'state',
            'filter_index' => 'main_table.state',
            'width' => '80px',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Translate',
            'type'      => 'options',
            'options'   => Mage::getModel('giftcard/adminhtml_system_config_source_giftcard_state')->toKeyValueArray()
        ));

        return parent::_prepareColumns();
    }

    protected function addExport()
    {
        $helper = Mage::helper('giftcard');
        $this->addExportType('*/*/exportXls', $helper->__('MS Excel .xlsx'));
        $this->addExportType('*/*/exportCsv', $helper->__('CSV'));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row) {
        return;
    }

    protected function _prepareMassaction()
    {
        $helper = Mage::helper('giftcard');
        $this->setMassactionIdField('main_table.entity_id');
        $this->getMassactionBlock()->setFormFieldName('giftcard');


        $statuses = Mage::getSingleton('giftcard/adminhtml_system_config_source_giftcard_status')->toOptionArray();
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> $helper->__('Change Status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $helper->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $states = Mage::getSingleton('giftcard/adminhtml_system_config_source_giftcard_state')->toOptionArray();
        $this->getMassactionBlock()->addItem('state', array(
            'label'=> $helper->__('Change State'),
            'url'  => $this->getUrl('*/*/massState', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'state',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => $helper->__('State'),
                    'values' => $states
                )
            )
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> $helper->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => $helper->__('Are you sure?')
        ));
        return $this;
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

        $path = Mage::getBaseDir('tmp');
        $name = md5(microtime());
        $file = $path . DS . $name . '.xlsx';

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