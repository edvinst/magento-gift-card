<?php

class MT_Giftcard_Block_Adminhtml_Giftcard_Series_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcard_series_list_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('giftcard/series_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('giftcard');

        $this->addColumn('entity_id', array(
            'header' => $helper->__(' #'),
            'index'  => 'entity_id',
            'width' => '60px',
        ));

        $this->addColumn('name', array(
            'header' => $helper->__('Series Name'),
            'index'  => 'name',
        ));

        $this->addColumn('value', array(
            'header' => $helper->__('Initial Value'),
            'index'  => 'value',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_Price',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'width' => '100px',
        ));


        $this->addColumn('lifetime', array(
            'header' => $helper->__('Active (days)'),
            'index'  => 'lifetime',
            'width' => '80px',
            'renderer'  => 'MT_Giftcard_Block_Adminhtml_Widget_Grid_Column_Renderer_days',
        ));

        $this->addExport();

        return parent::_prepareColumns();
    }

    protected function addExport()
    {
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addSeriesNameFilter($column);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $helper = Mage::helper('giftcard');
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('giftcardseries');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('catalog')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('_current'=>true)),
            'confirm' => $helper->__('Are you sure?'),
            'additional' => array(
                'visibility' => array(
                    'name' => 'delete_action',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Option'),
                    'values' => array(
                        array(
                            'label' => $helper->__('Delete Only Series'),
                            'value' => 0
                        ),
                        array(
                            'label' => $helper->__('Delete Series and Gift Cards'),
                            'value' => 1
                        ),
                    )
                )
            )
        ));
        return $this;
    }
}