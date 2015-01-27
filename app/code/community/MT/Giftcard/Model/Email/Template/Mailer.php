<?php

class MT_Giftcard_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    public function send()
    {
        if (!Mage::helper('giftcard')->isActive())
            return parent::send();

        $emailTemplate = Mage::getModel('core/email_template');
        //add gift card pdf to email
        $this->addGiftCardAttachment($emailTemplate);
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );
        }
        return $this;
    }

    public function addGiftCardAttachment($template)
    {
        $storeId = $this->getStoreId();
        $addPdf = false;

        if (($this->getTemplateId() == Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId)
            || $this->getTemplateId() == Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId))
            && Mage::getStoreConfig('giftcard/mail/pdf_order_new_mail', $storeId)) {
            $addPdf = true;
        }

        if ($this->getTemplateId() == Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE, $storeId)
            && Mage::getStoreConfig('giftcard/mail/pdf_order_invoice_mail', $storeId)) {
            $addPdf = true;
        }

        if ($addPdf) {
            $templateParams = $this->getTemplateParams();
            if (isset($templateParams['order'])) {
                $order = $templateParams['order'];

                $pdf = Mage::getModel('giftcard/giftcard_pdf');
                $pdf->setOrder($order);
                if ($pdf->createPdf()) {
                    $pdfFile = $pdf->getPdfPath();
                    $content = file_get_contents($pdfFile);
                    $attachment = new Zend_Mime_Part($content);
                    $attachment->type = 'application/pdf';
                    $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                    $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                    $attachment->filename = Mage::helper('giftcard')->__('gif_card_').$order->getIncrementId().'.pdf';
                    $template->getMail()->addAttachment($attachment);
                }
            }
        }

        return $this;
    }
}