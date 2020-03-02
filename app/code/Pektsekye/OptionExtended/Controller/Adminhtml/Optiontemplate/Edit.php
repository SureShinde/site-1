<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Edit extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
      $this->_initTemplate();
      $template = $this->_coreRegistry->registry('current_template');          
     
      $this->_view->loadLayout();     
      $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates');    
      $this->_view->getPage()->getConfig()->getTitle()->prepend($template->getId() ? $template->getTitle() : __('New Template'));       
      $this->_view->renderLayout();
  }

}
