<?php

class MT_Giftcard_Adminhtml_Giftcard_ExportController extends Mage_Adminhtml_Controller_Action
{
    protected function _prepareDownloadResponseAction()
    {
        $content = $this->getRequest()->getParam('content');
        $fileName = $this->getRequest()->getParam('file_name');
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/octet-stream', true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $content = file_get_contents($file);
                if (!empty($content['rm']))
                    unlink($file);

                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();
                ob_get_clean();
                echo $content;
                ob_end_flush();
                exit(0);
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }

    public function orderAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $format = $this->getRequest()->getParam('format');
        $fileName   = 'gift_cards_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.'.$format;

        try {
            $content = Mage::getSingleton('giftcard/giftcard_action')
            ->exportOrderGiftCard($orderId, $format);
            if (!file_exists($content['value']))
                throw new Exception(Mage::helper('giftcard')->__('Can not to create file'));

            $this->_forward('_prepareDownloadResponse', 'giftcard_export', null, array(
                'file_name' => $fileName,
                'content' => $content
            ));

            $this->_redirect('adminhtml/sales_order/view/', array(
                'order_id' => $orderId
            ));

        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/sales_order/view/', array('order_id' => $orderId));
    }
}
