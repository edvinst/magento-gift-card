<?php

class MT_Giftcard_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
        $parent = parent::_prepareLayout();
        //if (!Mage::helper('giftcard')->isActive())
        //    return $parent;

        $product = $this->getProduct();
        if ($product->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
            $this->addTab('giftcard_series_assign', array(
                'label' => Mage::helper('giftcard')->__('Assign Gift Cards Series'),
                'url'   => $this->getUrl('*/*/giftCardSeries', array('_current' => true)),
                'class' => 'ajax',
            ));

        return $this;
    }
}