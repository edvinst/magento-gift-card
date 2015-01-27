<?php

class MT_Giftcard_Model_Option extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/option');
    }

    public function getValues($product = null)
    {
        $values = array();
        if ($this->getId() && $product) {
            $values = Mage::getModel($this->getSourceModel())->getValues($product);
        }
        return $values;
    }

    public function loadByName($name)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('name', $name)
            ->setPageSize(1);
        if ($collection->count() == 1)
            $this->setData($collection->getFirstItem()->getData());
        return $this;
    }

    public function getPrice($product)
    {
        if (!$this->getId())
            $product->getPrice();
        $optionName = $this->getName();
        $productOption = $product->getCustomOption($optionName);
        if ($productOption) {
            $value = $productOption->getValue();
            $valueObj = Mage::getModel($this->getSourceModel())->getValue($value);
            return $valueObj->getPrice();
        }
    }
}
