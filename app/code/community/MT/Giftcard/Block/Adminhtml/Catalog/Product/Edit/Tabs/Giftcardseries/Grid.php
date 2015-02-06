<?php



class MT_Giftcard_Block_Adminhtml_Catalog_Product_Edit_Tabs_Giftcardseries_Grid
    extends MT_Giftcard_Block_Adminhtml_Giftcard_Series_List_Grid
{

    private $__selectedGiftCardSeries = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardseries_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
    }

    protected function addExport(){}

    protected function _prepareMassaction(){}

    public function getGridUrl()
    {
        return $this->getUrl('*/*/giftCardSeriesGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $giftCardIds = $this->_getSelectedGiftCardsSeries();
            if (empty($giftCardIds)) {
                $giftCardIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.entity_id', array('in' => $giftCardIds));
            } else {
                if($giftCardIds) {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('nin' => $giftCardIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function isReadonly()
    {
        return false;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_products',
            'values'            => $this->_getSelectedGiftCardsSeries(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));



        parent::_prepareColumns();
        $this->addColumn('position', array(
            'header'            => Mage::helper('giftcard')->__('Position'),
            'width'             => 1,
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'filter'            => false,
            'editable'          => true,
            'edit_only'         => !$this->_getProduct()->getId(),
            'renderer'  => 'adminhtml/widget_grid_column_renderer_input'

        ));

        parent::_prepareColumns();
        unset($this->_columns['store_id']);
    }

   public function _getSelectedGiftCardsSeries()
   {
       return array_keys($this->getSelectedGiftCardSeriesProducts());
   }

    public function getSelectedGiftCardSeriesProducts()
    {
        if ($this->__selectedGiftCardSeries == null) {
            $giftCardSeries = array();
            $collection = Mage::getModel('giftcard/series')->getCollectionByProduct($this->_getProduct()->getId());
            if (count($collection) > 0) {
                foreach ($collection as $item)
                    $giftCardSeries[$item->getId()] = array(
                        'gift_card_price' => $item->getGiftCardPrice(),
                        'position' => $item->getPosition(),
                    );
            }
            $this->__selectedGiftCardSeries = $giftCardSeries;
        }

        return $this->__selectedGiftCardSeries;
    }

}