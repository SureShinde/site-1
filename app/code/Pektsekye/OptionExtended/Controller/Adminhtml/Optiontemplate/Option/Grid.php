<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Grid extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
    $this->getResponse()->setBody(
        $this->_view->getLayout()->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid')->toHtml()
    );     
  }  

}
