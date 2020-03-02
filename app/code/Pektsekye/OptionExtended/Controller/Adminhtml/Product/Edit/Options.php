<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit;

class Options extends \Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit
{


  public function execute()
  {
      $this->_initProduct();
      $this->getResponse()->setBody(
          $this->_view->getLayout()->createBlock(
              'Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options',
              'optionextended'
          )->toHtml()
      );      
  } 

}
