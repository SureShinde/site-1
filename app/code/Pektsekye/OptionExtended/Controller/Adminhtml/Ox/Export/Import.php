<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export;

class Import extends \Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export
{


  public function execute()
  {
    if ($this->getRequest()->isPost() && $this->getRequest()->getFiles('import_file')) {
        try {
            $importType = $this->getRequest()->getPost('import_type');
            $importFile = $this->getRequest()->getFiles('import_file');
            
            $importHandler = $this->_objectManager->create('Pektsekye\OptionExtended\Model\CsvImportHandler');
            $importHandler->importFromCsvFile($importFile, $importType);  
                     
            switch($importType){
              case 'options':
                $this->messageManager->addSuccess(__('Product custom options have been imported.'));
                break;
              case 'values':
                $this->messageManager->addSuccess(__('Product custom option values have been imported.'));
                break; 
              case 'options_translate':
                $this->messageManager->addSuccess(__('Product custom options for translation have been imported.'));
                break;
              case 'values_translate':
                $this->messageManager->addSuccess(__('Product custom option values for translation have been imported.'));
                break; 
              case 'templates':
                $this->messageManager->addSuccess(__('Template entities have been imported.'));
                break;    
              case 'template_products':
                $this->messageManager->addSuccess(__('Template products have been imported.'));
                break;                              
              case 'template_options':
                $this->messageManager->addSuccess(__('Template options have been imported.'));
                break;
              case 'template_values':
                $this->messageManager->addSuccess(__('Template values have been imported.'));
                break; 
              case 'template_options_translate':
                $this->messageManager->addSuccess(__('Template options for translation have been imported.'));
                break;
              case 'template_values_translate':
                $this->messageManager->addSuccess(__('Template values for translation have been imported.'));
                break;                                                         
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage().'Invalid file upload attempt'));
        }
    } else {
        $this->messageManager->addError(__('Invalid file upload attempt'));
    }
    $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
  }

}
