<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Edit extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {
      $this->_initOption();
      $option = $this->_coreRegistry->registry('current_option');      
     
      $this->_view->loadLayout();     
      $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates');    
      $this->_view->getPage()->getConfig()->getTitle()->prepend($option->getId() ? $option->getTitle() : __('New Option'));       
      $this->_view->renderLayout();   
  }     

}
