<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class ProductsGrid extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
    $this->_initTemplate();
    $this->getResponse()->setBody(
        $this->_view->getLayout()->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit\Tab\Products\Grid')->toHtml()
    );    
  }

}
