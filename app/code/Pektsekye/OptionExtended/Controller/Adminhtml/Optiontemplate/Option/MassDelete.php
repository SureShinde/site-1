<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class MassDelete extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
    $ids = $this->getRequest()->getParam('ids');
    if(!is_array($ids)) {
      $this->messageManager->addError(__('Please select item(s)'));
    } else {
      try {
          $this->_oxTemplateOption->getResource()->deleteOptionsWithChidrenUpdate((int) $this->getRequest()->getParam('template_id'), $ids);

          $this->messageManager->addSuccess(__('Total of %1 record(s) were successfully deleted', count($ids)));
      } catch (\Exception $e) {
          $this->messageManager->addError($e->getMessage());
      }
    }      
    $this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));      
  } 

}
