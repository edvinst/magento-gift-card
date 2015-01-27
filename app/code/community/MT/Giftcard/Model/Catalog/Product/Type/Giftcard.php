<?php

class MT_Giftcard_Model_Catalog_Product_Type_Giftcard extends Mage_Catalog_Model_Product_Type_Abstract
{
    protected  $_canConfigure = true;

    public function hasOptions($product = null)
    {
        return true;
    }

    public function isSalable($product = null)
    {
        $seriesCollection = Mage::getModel('giftcard/series')
            ->getCollectionByProduct($product->getId(), true)
            ->setPageSize(1);
        return count($seriesCollection) == 1;
    }

    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        $attributes = $buyRequest->getData('giftcard_attribute');
        $options = Mage::getModel('giftcard/option')->getCollection();
        if ($options) {
            foreach ($options as $option) {
                if (isset($attributes[$option->getName()])) {

                    //is valid select option
                    if ($option->getType() == 'select') {
                        $availableValues = $option->getValues($product);
                        if ($option->getType() == 'select' && !isset($availableValues[$attributes[$option->getName()]]))
                            continue;
                    }

                    //apply for products
                    if (is_array($result)) {
                        foreach($result as $product){
                            $product->addCustomOption($option->getName(), $attributes[$option->getName()]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getSku($product = null)
    {
        $sku = $this->getProduct($product)->getData('sku');
        $option = $this->getProduct($product)->getCustomOption('giftcard_value');
        if ($option) {
            $optionValue = $option->getValue();
            $giftCardOption = Mage::getModel('giftcard/option')->loadByName('giftcard_value');
            $giftCardOptionValues = $giftCardOption->getValues($product);

            if (isset($giftCardOptionValues[$optionValue]) && is_numeric($giftCardOptionValues[$optionValue]))
                $sku.='-'.number_format($giftCardOptionValues[$optionValue],0,'','');
        }

        if ($this->getProduct($product)->getCustomOption('option_ids')) {
            $sku = $this->getOptionSku($product,$sku);
        }
        return $sku;
    }

    public function isVirtual($product = null)
    {
        return true;
    }

    public function hasAvailableRealGiftCard($product = null)
    {
        if ($product == null)
            return false;

        $seriesCollection = Mage::getModel('giftcard/series')
            ->getCollectionByProduct($product->getId(), true);
        $seriesCollection->addFieldToSelect('entity_id');

        $in = array();
        if ($seriesCollection->count() > 0) {
            foreach ($seriesCollection as $series) {
                $in[] = $series->getId();
            }
        }

        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('series_id', array('in' => $in))
            ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_ACTIVE)
            ->setPageSize(1);

        return  $giftCardCollection->count() == 1;
    }
}