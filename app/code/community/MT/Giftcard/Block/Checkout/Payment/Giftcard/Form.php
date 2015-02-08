<?php

class MT_Giftcard_Block_Checkout_Payment_Giftcard_Form
    extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('mt/giftcard/checkout/payment/giftcard/form.phtml');
    }

    public function getControllerUrl()
    {
        return Mage::getUrl('giftcard/checkout_cart');
    }

    public function isActive()
    {
        if (!Mage::helper('giftcard')->isActive() || !Mage::getStoreConfig('giftcard/cart/form_in_checkout'))
            return false;

        $cart = Mage::getModel('checkout/cart')->getQuote();
        $items = $cart->getAllItems();
        if (count($items) == 0)
            return false;

        foreach ($items as $item) {
            if ($item->getProductType() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
                return false;
        }

        return true;
    }

    public function isFormVisible()
    {
        if (Mage::app()->getRequest()->getParam('is_form_visible') == 0
            && count($this->getAppliedGiftCardCollection()) == 0
        )
            return false;

        return true;
    }

    public function getAppliedGiftCardCollection()
    {
        return Mage::getSingleton('giftcard/checkout_giftcard')->getQuoteGiftCardCollection();
    }

    public function getAction()
    {
        return Mage::getUrl('giftcard/checkout_cart/giftCardAjax');
    }
}