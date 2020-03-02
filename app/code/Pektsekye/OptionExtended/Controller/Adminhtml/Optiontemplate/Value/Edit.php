<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class Edit extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {
    $this->_initValue();
    $value = $this->_coreRegistry->registry('current_value');        
     
    $this->_view->loadLayout();     
    $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates');   
    $this->_view->getPage()->getConfig()->getTitle()->prepend($value->getId() ? $value->getTitle() : __('New Value'));      
    $this->_view->renderLayout();       
  }     

}
