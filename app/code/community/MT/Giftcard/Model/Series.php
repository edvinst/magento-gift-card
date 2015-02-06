<?php

class MT_Giftcard_Model_Series extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('giftcard/series');
    }

    public function getGiftCardGenerator()
    {
        return Mage::getSingleton('giftcard/series_generator');
    }

    public function deleteGiftCards()
    {
        $giftCardCollection = $this->getGiftCardCollection();

        if ($giftCardCollection->count() > 0) {
            foreach ($giftCardCollection as $giftCard)
                $giftCard->delete();
        }

        return true;
    }

    public function getGiftCardCollection()
    {
        if (!$this->getId())
            return null;

        $collection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('series_id', $this->getId())
            ->addFieldToFilter('status', array('neq' => MT_Giftcard_Model_Giftcard::STATUS_ACTIVE));

        return $collection;
    }

    public function getOptionArray()
    {
        $optionArray = array(array(
            'label' => '',
            'value' => 0,
        ));
        $collection = $this->getCollection();
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $optionArray[] = array(
                    'label' => $item->getName(),
                    'value' => $item->getId(),
                );
            }
        }
        return $optionArray;
    }

    public function getCollectionByProduct($productId, $currencyFilter = false)
    {
        if (!is_numeric($productId))
            return null;

        $collection = $this->getCollection();
        $table = Mage::getSingleton('core/resource')->getTableName('giftcard/series_product');
        $collection->getSelect()
            ->join(array('t1' => $table), 'main_table.entity_id=t1.giftcard_series_id', array(
                'position' => 't1.position',
            ))
            ->where('t1.product_id =?', $productId);

        return $collection;
    }

    public function getProductPrice($product, $seriesId)
    {
        $collection = $this->getCollectionByProduct($product->getId());
        $collection->addFieldToFilter('main_table.entity_id', $seriesId)
            ->setPageSize(1);
        if ($collection->count() == 1) {
            $series = $collection->getFirstItem()->getData();
            if (isset($series['gift_card_price'])
                && is_numeric($series['gift_card_price'])
                && $series['gift_card_price'] > 0
            ) {
                return $series['gift_card_price'];
            } else {
                return $series['value'];
            }
        }
        return false;
    }

    public function getFormatedValue()
    {
        return Mage::helper('core')->currency($this->getValue(), true, false);
    }



    public function getPrice()
    {
        if ($this->hasGiftCardPrice() && $this->getGiftCardPrice() > 0)
            $price = $this->getGiftCardPrice();
        else
            $price = $this->getValue();

        return Mage::getModel('directory/currency')->format(
            $price,
            array('display'=>Zend_Currency::NO_SYMBOL),
            false
        );
    }

    public function getOldPrice()
    {
        return Mage::getModel('directory/currency')->format(
            $this->getValue(),
            array('display'=>Zend_Currency::NO_SYMBOL),
            false
        );
    }

    public function getFormatedOldPrice()
    {
        return Mage::helper('core')->currency($this->getOldPrice(), true, false);
    }

    public function getFormatedPrice()
    {
        return Mage::helper('core')->currency($this->getPrice(), true, false);
    }

    public function addActiveGiftCardFilter($seriesCollection)
    {
        if ($seriesCollection->count() == 0)
            return $seriesCollection;

        $in = array();
        foreach ($seriesCollection as $series) {
            $in[] = $series->getId();
        }

        $giftCardCollection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('series_id', array('in' => $in))
            ->addFieldToFilter('status', MT_Giftcard_Model_Giftcard::STATUS_ACTIVE)
            ->addFieldToSelect('series_id');
        $giftCardCollection->getSelect()->group('series_id');

        $availableSeries = array();
        if ($giftCardCollection->count() != 0) {
            foreach ($giftCardCollection as $giftCard) {
                $availableSeries[] = $giftCard->getSeriesId();
            }
        }

        foreach ($seriesCollection as $key => $series) {
            if (!in_array($series->getId(), $availableSeries))
                $seriesCollection->removeItemByKey($key);
        }

        return $seriesCollection;
    }
}