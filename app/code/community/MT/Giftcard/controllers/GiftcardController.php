<?php

class MT_Giftcard_GiftcardController
    extends Mage_Core_Controller_Front_Action
{
    public function pdfAction()
    {

        if(!Mage::helper('customer')->isLoggedIn()){
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }

        $params = $this->getRequest()->getParams();
        if (isset($params['id']) && is_numeric($params['id'])) {
            $orderId = $params['id'];

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addFieldToFilter('entity_id', $orderId)
                ->setPageSize(1);

            if ($order = $orders->getFirstItem()) {
                $content = Mage::getSingleton('giftcard/giftcard_action')
                    ->exportOrderGiftCard($order->getId(), 'pdf');

                if (!file_exists($content['value']))
                    throw new Exception(Mage::helper('giftcard')->__('Can not to create file'));

                $pdfFile = $content['value'];
                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', 'application/octet-stream', true)
                    ->setHeader('Content-Length', filesize($pdfFile), true)
                    ->setHeader('Content-Disposition', 'attachment; filename="'.Mage::helper('giftcard')->__('gift_card').'_'.$order->getIncrementId().'.pdf"', true)
                    ->setHeader('Last-Modified', date('r'), true);
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();
                ob_get_clean();
                echo file_get_contents($pdfFile);
                ob_end_flush();
                unlink($pdfFile);
                exit(0);
            }
        }

        return $this;
    }
}
