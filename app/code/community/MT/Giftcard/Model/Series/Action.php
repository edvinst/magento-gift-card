<?php

class MT_Giftcard_Model_Series_Action
{
    public function giftCardsSeriesListDelete($giftCardSeriesIds, $deleteGiftCards = false)
    {
        if (count($giftCardSeriesIds) == 0)
            return false;

        foreach ($giftCardSeriesIds as $giftCardSeriesId)
            $this->giftCardSeriesDelete($giftCardSeriesId, $deleteGiftCards);

        return true;
    }

    public function giftCardSeriesDelete($giftCardSeriesId, $deleteGiftCards = false)
    {
        $giftCardSeries = Mage::getModel('giftcard/series')->load($giftCardSeriesId);
        if (!is_numeric($giftCardSeries->getId()))
            return false;

        if ($deleteGiftCards)
            $giftCardSeries->deleteGiftCards();

        $giftCardSeries->delete();

        return true;
    }

    public function assignGiftCardSeriesToProduct($productId, $giftCardsSeries)
    {
        if (!is_numeric($productId))
            throw new Exception(Mage::helper('giftcard')->__('Can not assign gift cards to products.'));
        $this->unlinkProductsGiftCardSeries($productId);
        if (count($giftCardsSeries) > 0) {
            $resource = Mage::getSingleton('core/resource');
            $db = $resource->getConnection('core');
            foreach ($giftCardsSeries as $seriesId => $params) {
                $db->insert('mt_giftcardseries_product', array(
                    'product_id' => $productId,
                    'giftcard_series_id' => $seriesId,
                    'position' => $params['position'],
                ));
            }
        }

        return true;
    }

    public function unlinkProductsGiftCardSeries($productId)
    {
        if (!is_numeric($productId))
            return false;
        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core');
        $db->delete('mt_giftcardseries_product', array('product_id='.$productId));
        return true;
    }

    public function addNewCodeToOrder($seriesId, $order, $item, $options = array())
    {
        $qty = $item->getQtyOrdered();
        $orderId = $order->getId();
        $helper = Mage::helper('giftcard');
        $giftCardSeries = Mage::getModel('giftcard/series')->load($seriesId);

        if ($qty <= 0)
            throw new Mage_Core_Exception($helper->__('Bad gift card item quantity'));

        if (!$giftCardSeries->getId())
            throw new Mage_Core_Exception($helper->__('Gift card series not found'));

        $giftCardCollection = null;
        $generator = Mage::getModel('giftcard/series')->getGiftCardGenerator();
        $generator->setData(array(
            'length' => $giftCardSeries->getDefaultLength(),
            'format' => $giftCardSeries->getDefaultFormat(),
            'prefix' => $giftCardSeries->getDefaultPrefix(),
            'suffix' => $giftCardSeries->getDefaultSuffix(),
            'dash' => $giftCardSeries->getDefaultDash(),
            'series_id' => $giftCardSeries->getId(),
            'qty' => $qty,
            'status' => MT_Giftcard_Model_Giftcard::STATUS_ACTIVE,
        ));
        $generator->generatePool();
        $giftCardCollection = $generator->getCollection();

        if (count($giftCardCollection) != $qty)
            throw new Mage_Core_Exception($helper->__('Not enough gift cards'));

        foreach ($giftCardCollection as $giftCard) {
            $giftCard->setOrderId($orderId);
            $giftCard->setOrderItemId($item->getId());

            if ($order->hasInvoices()) {
                $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_SOLD);
            } else {
                $giftCard->setStatus(MT_Giftcard_Model_Giftcard::STATUS_PENDING);
            }

            $giftCard->save();
        }

        return true;
    }
}