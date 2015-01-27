<?php

class MT_Giftcard_Block_Adminhtml_Sales_Order_View
    extends Mage_Adminhtml_Block_Sales_Order_View
{
    public function __construct()
    {
        parent::__construct();
        if (!Mage::helper('giftcard')->isActive())
            return;

        $order = $this->getOrder();
        $items = $order->getAllItems();

        if (count($items) != 0) {
            foreach ($items as $item) {
                if ($item->getProduct()->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT) {

                    $this->_addButton('download_pdf_giftcard', array(
                        'label'     => Mage::helper('sales')->__('Gift Card (.pdf)'),
                        'class'     => 'scalable',
                        'onclick' => 'downloadPdfGiftCard()',
                    ));

                    $this->_addButton('download_pdf_giftcard_zipped', array(
                        'label'     => Mage::helper('sales')->__('Gift Card (.jpg zipped)'),
                        'class'     => 'scalable',
                        'onclick' => 'downloadImageGiftCard()',
                    ));

                    $downloadPdfLink = Mage::helper("adminhtml")->getUrl("adminhtml/giftcard_export/order", array(
                        'id' => $order->getId(),
                        'format' => 'pdf'
                    ));

                    $downloadImageLink = Mage::helper("adminhtml")->getUrl("adminhtml/giftcard_export/order", array(
                        'id' => $order->getId(),
                        'format' => 'zip'
                    ));

                    $this->_formScripts[] = " function downloadPdfGiftCard(){ window.location.href='".$downloadPdfLink."'; } ";
                    $this->_formScripts[] = " function downloadImageGiftCard(){ window.location.href='".$downloadImageLink."'; } ";
                    break;
                }
            }
        }
    }
}