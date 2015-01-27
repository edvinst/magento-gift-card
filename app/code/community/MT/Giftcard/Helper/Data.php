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

    public function prepareQuoteGiftCard($currentValue, $giftCardCode)
    {
        if (empty($giftCardCode))
            return $currentValue;

        if ($this->isGiftCardCodeAddedToQuote($currentValue, $giftCardCode))
            return $currentValue;

        $codes = unserialize($currentValue);

        if (!is_array($codes))
            $codes = array();

        $codes[] = array('code' => $giftCardCode);
        return serialize($codes);
    }

    public function isGiftCardCodeAddedToQuote($currentValue, $giftCardCode)
    {
        if (empty($giftCardCode))
            return true;

        $codes = unserialize($currentValue);

        if (!is_array($codes))
            return false;

        foreach ($codes as $code) {
            if ($code['code'] == $giftCardCode)
                return true;
        }

        return false;
    }

    public function removeQuoteGiftCardCodes($currentValue, $removeCodes)
    {
        if (count($removeCodes) == 0)
            return $currentValue;

        $codes = unserialize($currentValue);

        if (!is_array($codes))
            return $currentValue;

        foreach ($codes as $key => $appliedCode) {
            foreach ($removeCodes as $removeCode) {
                if ($appliedCode['code'] == $removeCode)
                    unset($codes[$key]);
            }
        }

        return serialize($codes);
    }

    public function getGiftCardCodeArray($currentValue)
    {
        $codes = array();
        $giftCardData = unserialize($currentValue);

        if (!is_array($giftCardData))
            return $codes;

        foreach ($giftCardData as $code) {
            $codes[] = $code['code'];
        }

        return $codes;
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

}