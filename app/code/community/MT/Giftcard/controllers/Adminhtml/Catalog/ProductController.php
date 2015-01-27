<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php');

class MT_Giftcard_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /*public function giftCardTabAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog_product_edit_tabs_giftcard_form');
        //->setProductsCustom($this->getRequest()->getPost('products_giftcard', null));
        $this->renderLayout();
    }*/

    public function giftCardSeriesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog_product_edit_tabs_giftcardseries_grid');
            //->setProductsCustom($this->getRequest()->getPost('products_giftcard', null));
        $this->renderLayout();
    }

    public function giftCardSeriesGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog_product_edit_tabs_giftcardseries_grid');
            //->setProductsRelated($this->getRequest()->getPost('products_giftcard', null));
        $this->renderLayout();
    }
}
