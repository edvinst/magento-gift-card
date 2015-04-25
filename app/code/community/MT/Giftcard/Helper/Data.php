<?php

class MT_Giftcard_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isActive()
    {
        return Mage::getStoreConfig('giftcard/general/is_active');
    }

    public function getProductCurrencyCode(Mage_Catalog_Model_Product $product)
    {
        return Mage::app()->getStore($product->getStoreId())->getCurrentCurrencyCode();
    }

    public function getCharset($format)
    {
        $ab = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '01234567890';

        switch ($format) {
            case 'alphanumeric':
                $chars = $ab.$num;
                break;
            case 'alphabetical':
                $chars = $ab;
                break;
            case 'numeric':
                $chars = $num;
                break;
        }

        return str_split($chars);
    }

    public function getExportFileTmpName($ext)
    {
        $path = Mage::getBaseDir('tmp');
        $name = md5(microtime().Mage::getBaseUrl().rand(0, 99999));
        $file = $path . DS . $name . $ext;
        return $file;
    }

    public function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        return $rgb;
    }

    public function getGiftCardBackgroundDir()
    {
        return Mage::getBaseDir('media') . DS.'mt'. DS .'giftcard'. DS.'template'. DS;
    }

    public function isAjax()
    {
        $request = Mage::app()->getRequest();
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        if ($request->getParam('ajax') || $request->getParam('isAjax')) {
            return true;
        }
        return false;
    }

    public function isPaymentMethodFormVisible()
    {
        $quoteId = Mage::getSingleton('checkout/cart')->getQuote()->getId();
        $giftCardCollection = Mage::getSingleton('giftcard/quote')->getGiftCardCollection($quoteId);
        if (Mage::app()->getRequest()->getParam('is_form_visible') == 0
            && $giftCardCollection->count() == 0
        ) {
            return false;
        }
        return true;
    }

    public function hasGiftCardProductInCart()
    {
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $items = $cart->getAllItems();
        if (count($items) == 0)
            return false;

        foreach ($items as $item) {
            if ($item->getProduct()->getTypeId() == MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
                return true;
        }

        return false;
    }

    public function convertToCurrentCurrency($price, $currencyCode)
    {
        return Mage::helper('directory')->currencyConvert(
            $price,
            $currencyCode,
            Mage::app()->getStore()->getCurrentCurrencyCode()
        );
    }
}