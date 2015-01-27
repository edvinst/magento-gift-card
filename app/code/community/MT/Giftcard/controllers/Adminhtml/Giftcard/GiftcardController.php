<?php

class MT_Giftcard_Adminhtml_Giftcard_GiftcardController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Gift Card'))->_title($this->__('Manage'));
        $this->loadLayout();
        $this->_setActiveMenu('giftcard/giftcard');

        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_list'));
        $this->renderLayout();
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $giftCard = Mage::getModel('giftcard/giftcard')
                ->load($id);
            try {
                $giftCard->delete();
                $this->_getSession()->addSuccess($this->__('The gift card has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function massStatusAction()
    {
        $giftCardIds = (array)$this->getRequest()->getParam('giftcard');
        $status = $this->getRequest()->getParam('status');
        $comeFrom = $this->getRequest()->getParam('come_from');

        try {
            Mage::getSingleton('giftcard/giftcard_action')
                ->updateAttributes($giftCardIds, array('status' => $status));

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($giftCardIds))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the gift card(s) status.'));
        }

        if (!empty($comeFrom)) {
            $this->_redirect(base64_decode($comeFrom));
        } else
            $this->_redirect('*/*/');
    }

    public function massStateAction()
    {
        $giftCardIds = (array)$this->getRequest()->getParam('giftcard');
        $states = $this->getRequest()->getParam('state');
        $comeFrom = $this->getRequest()->getParam('come_from');

        try {
            Mage::getSingleton('giftcard/giftcard_action')
                ->updateAttributes($giftCardIds, array('state' => $states));

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($giftCardIds))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the gift card(s) status.'));
        }

        if (!empty($comeFrom)) {
            $this->_redirect(base64_decode($comeFrom));
        } else
            $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $giftCardIds = (array)$this->getRequest()->getParam('giftcard');
        $comeFrom = $this->getRequest()->getParam('come_from');

        try {
            Mage::getSingleton('giftcard/giftcard_action')
                ->giftCardsDelete($giftCardIds);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been deleted.', count($giftCardIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while deleting the gift card(s).'));
        }
        if (!empty($comeFrom))
            $this->_redirect(base64_decode($comeFrom));
        else
            $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function exportXlsAction()
    {
        $fileName   = 'gift_cards_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.xlsx';
        $grid       = $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_list_grid');

        $this->_forward('_prepareDownloadResponse', 'giftcard_export', null, array(
            'file_name' => $fileName,
            'content' => $grid->getXlsFile()
        ));

    }

    public function exportCsvAction()
    {
        $fileName   = 'gift_cards_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.csv';

        $content    = $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function downloadAction()
    {
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('giftcard');

        try {
            if (!isset($params['id']) || !is_numeric($params['id']) || !isset($params['format']))
                throw new Mage_Core_Exception($helper->__('Bad Request'));

            $giftCard = Mage::getModel('giftcard/giftcard')->load($params['id']);
            if (!$giftCard->getId())
                throw new Mage_Core_Exception($helper->__('Bad Gift Card Id'));

            if (!$giftCard->getTemplateId())
                throw new Mage_Core_Exception($helper->__('Please, choose gift card template'));

            $filePath = '';
            $fileName = '';
            if ($params['format'] == 'pdf') {
                $pdf = Mage::getModel('giftcard/giftcard_pdf');
                $pdf->setGiftCard($giftCard);
                if ($pdf->createGiftCardPdf()) {
                    $filePath = $pdf->getPdfPath();
                    $fileName = Mage::helper('giftcard')->__('gift_card').'_'.$giftCard->getId().'.pdf';
                }

            } elseif ($params['format'] == 'jpg') {
                $draw = Mage::getModel('giftcard/giftcard_draw');
                $draw->setGiftCard($giftCard);
                $draw->setTemplate($giftCard->getTemplate());
                $draw->drawGiftCard();
                $filePath = $draw->getImagePath();
                $fileName = Mage::helper('giftcard')->__('gift_card').'_'.$giftCard->getId().'.jpg';
            }

            if ($filePath != '' && $fileName != '') {
                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', 'application/octet-stream', true)
                    ->setHeader('Content-Length', filesize($filePath), true)
                    ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"', true)
                    ->setHeader('Last-Modified', date('r'), true);
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();
                echo file_get_contents($filePath);
                unlink($filePath);
                exit(0);
            }

        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit/', array('id' => $params['id']));
    }
}