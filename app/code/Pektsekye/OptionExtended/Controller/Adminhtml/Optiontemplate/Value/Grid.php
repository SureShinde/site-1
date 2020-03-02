<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class Grid extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {
    $this->getResponse()->setBody(
        $this->_view->getLayout()->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Grid')->toHtml()
    );     
  }   



}
