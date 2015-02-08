<?php

class MT_Giftcard_Model_Giftcard_Action
{
    public function updateAttributes($giftCardIds, $attributesUpdate)
    {
        if (is_array($giftCardIds)) {
            foreach ($giftCardIds as $gifCardId) {
                $this->updateAttribute($gifCardId, $attributesUpdate);
            }
        } else {
            $this->updateAttribute($giftCardIds, $attributesUpdate);
        }
    }

    public function updateAttribute($giftCardId, $attributesUpdate)
    {
        $giftCard = Mage::getModel('giftcard/giftcard')->load($giftCardId);
        if (!is_numeric($giftCard->getId()))
            return false;

        foreach ($attributesUpdate as $attributeCode => $newValue)
            $giftCard->setData($attributeCode, $newValue);
        try {
            $giftCard->save();
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while saving the gift card.'));
        }
        return true;
    }

    public function giftCardsDelete($giftCardIds)
    {
        if (count($giftCardIds) == 0)
            return false;

        foreach ($giftCardIds as $giftCardId)
            $this->giftCardDelete($giftCardId);
    }

    public function giftCardDelete($giftCardId)
    {
        $giftCard = Mage::getModel('giftcard/giftcard')->load($giftCardId);
        $giftCard->delete();
    }

    public function exportGiftCardsCodes($giftCardIds, $format)
    {
        $collection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $giftCardIds));

        switch ($format) {
            case 'xlsx':
                $adapter = Mage::getModel('giftcard/export_adapter_excel');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.xlsx');
                break;
            case 'csv':
                $adapter = Mage::getModel('giftcard/export_adapter_csv');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.csv');
                break;
        }

        $export = Mage::getModel('giftcard/export_collection');
        $export->setCollection($collection);
        $export->setAdapter($adapter);
        $export->exportToFile($file);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    public function assignGiftCardToOrder($order)
    {

        $items = $order->getAllVisibleItems();
        $seriesAction = Mage::getModel('giftcard/series_action');

        foreach ($items as $item) {

            if ($item->getProductType() != MT_Giftcard_Model_Catalog_Product_Type::TYPE_GIFTCARD_PRODUCT)
                continue;

            $option = $item->getProductOptions();
            $optionData = $option['info_buyRequest'];
            if (!isset($optionData['giftcard_attribute']['giftcard_value']) || !is_numeric($optionData['giftcard_attribute']['giftcard_value']))
                throw new Exception('Gift card value is not selected');

            $giftCardSeriesId = $optionData['giftcard_attribute']['giftcard_value'];
            $seriesAction->addNewCodeToOrder($giftCardSeriesId, $order, $item, $optionData['giftcard_attribute']);
        }

    }

    public function exportOrderGiftCard($orderId, $format)
    {
        $giftCardIds = array();
        $collection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('order_id', $orderId);
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $giftCardIds[] = $item->getId();
            }
        }

        return $this->exportGiftCardList($giftCardIds, $format);
    }
    public function exportGiftCardList($giftCardIds, $format)
    {
        $collection = Mage::getModel('giftcard/giftcard')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $giftCardIds));

        $export = null;
        switch ($format) {
            case 'xlsx':
                $adapter = Mage::getModel('giftcard/export_adapter_excel');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.xlsx');
                $export = Mage::getModel('giftcard/export_collection');
                break;
            case 'csv':
                $adapter = Mage::getModel('giftcard/export_adapter_csv');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.csv');
                $export = Mage::getModel('giftcard/export_collection');
                break;
            case 'pdf':
                $adapter = Mage::getModel('giftcard/export_adapter_pdf');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.pdf');
                $export = Mage::getModel('giftcard/export_object');
                break;
            case 'zip':
                $adapter = Mage::getModel('giftcard/export_adapter_zip');
                $file = Mage::helper('giftcard')->getExportFileTmpName('.zip');
                $export = Mage::getModel('giftcard/export_object');
                break;
        }

        if ($export == null)
            throw new Mage_Core_Exception(Mage::helper('giftcard')->__('Bad file format'));

        $export->setCollection($collection);
        $export->setAdapter($adapter);
        $export->exportToFile($file);

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }
}