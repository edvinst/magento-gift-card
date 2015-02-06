<?php

class MT_Giftcard_Adminhtml_Giftcard_SeriesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Gift Card'))->_title($this->__('Series'));
        $this->loadLayout();
        $this->_setActiveMenu('giftcard/series');

        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_list'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        $model = Mage::getModel('giftcard/series');
        if ($id) {
            $model->load((int) $id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('gift card series does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('giftcard_series_data', $model);

        $this->_title($this->__('Gift Card Series'))->_title($this->__('Edit'));

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_edit'))
            ->_addLeft($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_edit_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('giftcard/series');
            $id = $this->getRequest()->getParam('id');
            if (is_numeric($id))
                $model->load($id);

            if (isset($data['series'])) {
                foreach ($data['series'] as $key => $value) {
                    $model->setData($key, $value);
                }
                //$model->setCurrency(Mage::app()->getStore($model->getStoreId())->getCurrentCurrencyCode());
            }

            Mage::getSingleton('adminhtml/session')->setFormData($data);

            try {
                $model->save();
                if (!$model->getId())
                    Mage::throwException(Mage::helper('giftcard')->__('Error saving gift card series'));

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftcard')->__('Gift card series was successfully saved.'));
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
            $giftCardSeries = Mage::getModel('giftcard/series')
                ->load($id);
            try {
                $giftCardSeries->delete();
                $this->_getSession()->addSuccess($this->__('The gift card series has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function deleteAllAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $giftCardSeries = Mage::getModel('giftcard/series')
                ->load($id);
            try {
                $giftCardSeries->deleteGiftCards();
                $giftCardSeries->delete();
                $this->_getSession()->addSuccess($this->__('The gift card series has been deleted. The gift cards has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    public function massDeleteAction()
    {

        $giftCardSeriesIds = (array)$this->getRequest()->getParam('giftcardseries');
        $action = $this->getRequest()->getParam('delete_action');

        try {
            Mage::getSingleton('giftcard/series_action')
                ->giftCardsSeriesListDelete($giftCardSeriesIds, $action);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been deleted.', count($giftCardSeriesIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while deleting the gift card(s) sieries.'));
        }

        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        $helper = Mage::helper('giftcard');
        $model = Mage::getModel('giftcard/series')->load($this->getRequest()->getParam('series_id'));
        Mage::register('giftcard_series_data', $model);

        $this->getResponse()->setBody($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_list_grid')->toHtml());
    }

    public function gridGiftCardAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        $model = Mage::getModel('giftcard/series')->load($this->getRequest()->getParam('series_id'));
        Mage::register('giftcard_series_data', $model);

        $this->getResponse()->setBody($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_series_edit_tabs_generate_grid')->toHtml());
    }

}
