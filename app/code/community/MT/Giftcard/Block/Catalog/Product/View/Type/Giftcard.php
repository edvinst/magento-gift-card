<?php

class MT_Giftcard_Block_Catalog_Product_View_Type_Giftcard extends Mage_Catalog_Block_Product_View_Abstract
{
    protected $_seriesCollection = null;

    public function hasOptions()
    {
        return true;
    }

    public function getAllowGiftCardSeries()
    {
        $product = $this->getProduct();
        $availableSeries = Mage::getModel('giftcard/series')->getCollectionByProduct($product->getId(), true);
        return $availableSeries;
    }

    public function getJsConfig()
    {
        $config = array();
        $options = array();
        /* value selector option */
        $seriesCollection = $this->getAllowGiftCardSeries();
        if ($seriesCollection->count() > 0) {
            foreach ($seriesCollection as $series) {
                $options[$series->getId()] = array(
                    'id' => $series->getId(),
                   // 'label' => $series->getPrice().$series->getCurrency(),
                    'price' => $series->getPrice(),
                    'formatedPrice' => $series->getFormatedPrice(),
                    'oldPrice' => $series->getOldPrice(),
                );
            }
        }
        $valueSelector = $this->getValueSelect();
        $config[$valueSelector['id']] = $valueSelector;
        $config[$valueSelector['id']]['options'] = $options;

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getValueSelect()
    {
        return array(
            'id' => 1,
            'label' => Mage::helper('giftcard')->__('Gift Card Value'),
            'name' => 'value'
        );
    }
}