<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export;

class Export extends \Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export
{


  public function execute()
  {
    $csvFileData = null;

    $importType = $this->getRequest()->getPost('export_type');
             
    switch($importType){
      case 'options':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Option');
        $content = $model->getOptionsCsv();
        $csvFileData = $this->_fileFactory->create('product_options.csv', $content); 
        break;
      case 'values':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Value');
        $content = $model->getValuesCsv();
        $csvFileData = $this->_fileFactory->create('product_option_values.csv', $content);
        break; 
      case 'options_translate':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Option');
        $content = $model->getOptionsTranslateCsv();
        $csvFileData = $this->_fileFactory->create('product_options_translate.csv', $content);
        break;
      case 'values_translate':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Value');
        $content = $model->getValuesTranslateCsv();
        $csvFileData = $this->_fileFactory->create('product_values_translate.csv', $content); 
        break; 
      case 'templates':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template');
        $content = $model->getTemplatesCsv();
        $csvFileData = $this->_fileFactory->create('template_entities.csv', $content); 
        break;    
      case 'template_products':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template');
        $content = $model->getTemplateProductsCsv();
        $csvFileData = $this->_fileFactory->create('template_products.csv', $content); 
        break;                              
      case 'template_options':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template\Option');
        $content = $model->getOptionsCsv();
        $csvFileData = $this->_fileFactory->create('template_options.csv', $content);
        break;
      case 'template_values':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template\Value');
        $content = $model->getValuesCsv();
        $csvFileData = $this->_fileFactory->create('template_values.csv', $content);
        break; 
      case 'template_options_translate':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template\Option');
        $content = $model->getOptionsTranslateCsv();
        $csvFileData = $this->_fileFactory->create('template_options_translate.csv', $content); 
        break;
      case 'template_values_translate':
        $model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Template\Value');
        $content = $model->getValuesTranslateCsv();
        $csvFileData = $this->_fileFactory->create('template_values_translate.csv', $content); 
        break;                                                         
    }
     
    return $csvFileData;
  }

}
