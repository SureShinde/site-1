<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Import extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
    $this->_view->loadLayout();          
    $this->_setActiveMenu('Pektsekye_OptionExtended::ox_templates');
    $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Import Options From Product'));          
    $this->_view->renderLayout();       
	}

}
