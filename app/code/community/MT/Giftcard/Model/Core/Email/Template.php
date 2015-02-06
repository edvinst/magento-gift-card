<?php

class MT_Giftcard_Model_Core_Email_Template extends MT_Giftcard_Model_Core_Email_Template_Init
{

    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        $this->addGiftCardAttachment($templateId, $vars, $storeId);
        return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
    }

    protected function addGiftCardAttachment($templateId, $vars, $storeId = null)
    {
        $addPdf = false;

        if (($templateId == Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId)
            || $templateId == Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId))
            && Mage::getStoreConfig('giftcard/mail/pdf_order_new_mail', $storeId)) {
            $addPdf = true;
        }

        if ($templateId == Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE, $storeId)
            && Mage::getStoreConfig('giftcard/mail/pdf_order_invoice_mail', $storeId)) {
            $addPdf = true;
        }

        if ($addPdf) {
            if (isset($vars['order'])) {
                $order = $vars['order'];
                $collection = Mage::getModel('giftcard/giftcard')->getCollection()
                    ->addFieldToFilter('order_id', $order->getId());
                if ($collection->count() <= 0) {
                    return;
                }
                $contentFile = Mage::getSingleton('giftcard/giftcard_action')
                    ->exportOrderGiftCard($order->getId(), 'pdf');

                if (!file_exists($contentFile['value']))
                    throw new Exception(Mage::helper('giftcard')->__('Can not to create file'));

                $content = file_get_contents($contentFile['value']);
                $attachment = new Zend_Mime_Part($content);
                $attachment->type = 'application/pdf';
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                $attachment->filename = Mage::helper('giftcard')->__('gif_card_').$order->getIncrementId().'.pdf';
                $this->getMail()->addAttachment($attachment);

                //delete pdf file from server
                if ($contentFile['rm'] == 1) {
                    unlink($contentFile['value']);
                }
            }
        }

        return;
    }
}