<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Index extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {
      $template = $this->_oxTemplate->load((int) $this->getRequest()->getParam('template_id'));
      
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
              __('Options')              
      );      
      $this->_view->getPage()->getConfig()->getTitle()->prepend(__('%1 - Options', $template->getTitle()));      
      $this->_view->renderLayout();  
  }

}
