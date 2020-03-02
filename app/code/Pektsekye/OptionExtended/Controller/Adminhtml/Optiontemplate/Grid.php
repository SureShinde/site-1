<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Grid extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
    $this->getResponse()->setBody(
        $this->_view->getLayout()->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Grid')->toHtml()
    );     
  }

}
