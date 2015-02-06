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


    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        $model = Mage::getModel('giftcard/giftcard');
        if ($id) {
            $model->load((int) $id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('giftcard does not exist'));
                $this->_redirect('*/*/');
            }
            Mage::register('giftcard_data', $model);
        } else {
            Mage::unregister('giftcard_data');
        }

        $this->_title($this->__('GiftCard'))->_title($this->__('Edit'));

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_edit'))
            ->_addLeft($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_edit_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('giftcard/giftcard');
            $id = $this->getRequest()->getParam('id');
            if (is_numeric($id))
                $model->load($id);

            try {
                if ($model->codeExist($data['code'])) {
                    if ($model->getCode() != $data['code'])
                        Mage::throwException(Mage::helper('giftcard')->__('Gift card code already exist'));
                }
                $statusBefore = $model->getStatus();
                if ($data) {
                    $skipFields = array('form_key');
                    foreach ($data as $key => $value) {
                        if (in_array($key, $skipFields))
                            continue;
                        $model->setData($key, $value);
                    }

                    if (!is_numeric($id))
                        $model->setBalance($model->getValue());

                    if ($statusBefore != MT_Giftcard_Model_Giftcard::STATUS_SOLD && $model->getStatus() == MT_Giftcard_Model_Giftcard::STATUS_SOLD&& !$model->getExpiredAt()) {
                        $model->setExpiredAt();
                    }
                }

                $model->save();
                if (!$model->getId())
                    Mage::throwException(Mage::helper('giftcard')->__('Error saving gift card'));

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftcard')->__('Gift card was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back'))
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                else
                    $this->_redirect('*/*/');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId())
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                else
                    $this->_redirect('*/*/new');
            }
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('No data found to save'));
        $this->_redirect('*/*/');
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

        if (!empty($comeFrom)){
            echo base64_decode($comeFrom);exit;
            $this->_redirect(base64_decode($comeFrom));
        }
        $this->_redirect('*/*/');
    }

    public function massExportAction()
    {
        $giftCardIds = (array)$this->getRequest()->getParam('giftcard');
        $format = $this->getRequest()->getParam('format');

        $fileName   = 'gift_cards_codes_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.'.$format;

        $content = Mage::getSingleton('giftcard/giftcard_action')
            ->exportGiftCardsCodes($giftCardIds, $format);

        $this->_forward('_prepareDownloadResponse', 'giftcard_export', null, array(
            'file_name' => $fileName,
            'content' => $content
        ));


        $comeFrom = $this->getRequest()->getParam('come_from');
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

    public function importAction()
    {
        $this->_title($this->__('Gift Card'))->_title($this->__('Import'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_giftcard_import'));
        $this->renderLayout();
    }

    public function importDataAction()
    {

        $params = $this->getRequest()->getParams();
        $back = 'import';
        if(isset($_FILES['file']['name']) && (file_exists($_FILES['file']['tmp_name']))) {
            $file = $_FILES['file'];
            try {
                $uploader = new Varien_File_Uploader('file');
                $uploader->setAllowedExtensions(array('csv','xlsx'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('tmp').DS;
                $uploader->save($path, $file['name']);
                $filePath = $path.$file['name'];

                $importer = Mage::getModel('giftcard/import_code');
                $importer->setEmptyFieldsData($params);
                $importer->importData($filePath);

                //if ($params['action'] == 'codes') {
                //} else {
                //    $importer = Mage::getModel('giftcard/import_giftcard');
                //    $importer->importData($filePath);
                //}

                unlink($filePath);
                $imported = $importer->getImportedItemCount();
                $skipped = $importer->getSkippedItemCount();
                $this->_getSession()->addSuccess(
                    $this->__('Imported: %d, Skipped: %d', $imported, $skipped)
                );
                $back = 'index';
            } catch (Mage_Core_Model_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/'.$back.'/');
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

    public function massCardExportAction()
    {
        $giftCardIds = (array)$this->getRequest()->getParam('giftcard');
        $format = $this->getRequest()->getParam('export_format');
        $fileName   = 'gift_cards_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.'.$format;

        try {
            $content = Mage::getSingleton('giftcard/giftcard_action')
                ->exportGiftCardList($giftCardIds, $format);
            $this->_forward('_prepareDownloadResponse', 'giftcard_export', null, array(
                'file_name' => $fileName,
                'content' => $content
            ));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $comeFrom = $this->getRequest()->getParam('come_from');
        if (!empty($comeFrom))
            $this->_redirect(base64_decode($comeFrom));
        else
            $this->_redirect('*/*/');
    }
}