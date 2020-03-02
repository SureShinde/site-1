<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Index extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
  
      $this->_view->loadLayout();   
      $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates')
          ->_addBreadcrumb(
              __('Catalog'),
              __('Catalog'))
          ->_addBreadcrumb(
              __('Option Templates'),
              __('Option Templates')
      );  
      $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Option Templates'));            
      $this->_view->renderLayout();

  }

}
