<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class Index extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {
      $this->_coreRegistry->register('current_template_id', (int) $this->getRequest()->getParam('template_id'));
      $this->_coreRegistry->register('current_option_id', (int) $this->getRequest()->getParam('option_id'));      
      
      $template = $this->_oxTemplate->load((int) $this->getRequest()->getParam('template_id'));
      $option = $this->_oxTemplateOption->load((int) $this->getRequest()->getParam('option_id'));
      $option->loadStoreFields(0);     
        
      $this->_view->loadLayout();
      $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates')
          ->_addBreadcrumb(
              __('Catalog'),
              __('Catalog'))
          ->_addBreadcrumb(
              __('Option Templates'),
              __('Option Templates'))
          ->_addBreadcrumb(
              __('Options'),
              __('Options'))  
          ->_addBreadcrumb(
              __('Values'),
              __('Values')                             
      );      
      $this->_view->getPage()->getConfig()->getTitle()->prepend(__('%1 - %2 - Values', $template->getTitle(), $option->getTitle()));      
      $this->_view->renderLayout(); 
  }      

}
