<?php

class MT_Giftcard_Adminhtml_Giftcard_TemplateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Gift Card'))->_title($this->__('Template'));
        $this->loadLayout();
        $this->_setActiveMenu('giftcard/giftcard');

        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_template_list'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        if (!Mage::helper('giftcard')->isAjax()) {
            $this->_forward('noRoute');
            return;
        }

        $this->getResponse()->setBody($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_template_list_grid')->toHtml());
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        $model = Mage::getModel('giftcard/template');
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
        }
        Mage::register('gift_card_template_data', $model);

        $this->_title($this->__('Gift Card Template'))->_title($this->__('Edit'));

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_template_edit'));
        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_template_edit_js'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('giftcard/template');
            $id = $this->getRequest()->getParam('id');
            $generalFields = $this->getRequest()->getParam('general');

            if (is_numeric($id))
                $model->load($id);

            if ($generalFields) {
                $designOptions = $this->getRequest()->getParam($generalFields['design']);

                $data = $generalFields;
                if (is_array($designOptions))
                    $data = array_merge($data, $designOptions);
                foreach ($data as $key => $value)
                    $model->setData($key, $value);
            }

            Mage::getSingleton('adminhtml/session')->setFormData($data);

            try {
                $model->save();
                if (!$model->getId())
                    Mage::throwException(Mage::helper('giftcard')->__('Error saving gift card template'));

                $designName = $model->getDesign();
                if(is_array($designOptions) && isset($_FILES[$designName.'_background']['name']) && (file_exists($_FILES[$designName.'_background']['tmp_name']))) {
                    $uploader = new Varien_File_Uploader($designName.'_background');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::helper('giftcard')->getGiftCardBackgroundDir();
                    $fileName = 'background'.$model->getId().'.jpg';
                    $filePath = $path.$fileName;
                    if (file_exists($filePath))
                        unlink($filePath);
                    $uploader->save($path, $fileName);
                    $model->setImage($fileName);
                    $model->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftcard')->__('Gift card template was successfully saved.'));
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
                    $this->_redirect('*/*/');
            }
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $giftCard = Mage::getModel('giftcard/template')
                ->load($id);
            try {
                $giftCard->delete();
                $this->_getSession()->addSuccess($this->__('The gift card template has been deleted.'));
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
        $cameFrom = $this->getRequest()->getParam('come_from');

        try {
            Mage::getSingleton('giftcard/giftcard_action')
                ->updateAttributes($giftCardIds, array('status' => $status));

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($giftCardIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the gift card(s) status.'));
        }

        if (!empty($cameFrom)) {
            $this->_redirect($cameFrom);
        } else
            $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = (array)$this->getRequest()->getParam('giftcard');

        try {
            Mage::getSingleton('giftcard/template_action')
                ->giftCardsTemplateDelete($ids);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been deleted.', count($ids))
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

        $this->_redirect('*/*/');
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

    public function previewAction()
    {
        $params = $this->getRequest()->getParams();

        $giftCard = Mage::getModel('giftcard/giftcard');
        $giftCard->setData(array(
            'value' => 50,
            'currency' => 'usd',
            'store_id' => 0,
            'template_id' => $params['template_id'],
            'code' => 'xmas-2564-4585',
            'expired_at' => date('Y-m-d', strtotime('+90day')),
        ));

        $template = Mage::getModel('giftcard/template');
        if (isset($params['template_id']))
            $template->load($params['template_id']);
        if (isset($params['background']))
            unset($params['background']);
        if (count($params) != 0) {
            $designName = $params['design'];
            $design = Mage::getModel('giftcard/giftcard_design_'.$designName);
            $fields = $design->getAdditionalFields();
            foreach ($fields as $field) {
                if (isset($params[$field[0]]))
                    $template->setData($field[2]['index'], urldecode($params[$field[0]]));
                else
                    $template->setData($field[2]['index'], '');
            }

            $template->setDesign($design);
        }
        $design->setGiftCard($giftCard);
        $design->setTemplate($template);
        $design->draw();

        //TODO change this
        header('Content-type:image/jpg');
        echo $design->getImageSource();
        exit;
    }
}
