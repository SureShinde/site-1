<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export;

class Index extends \Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Export
{


  public function execute()
  {
  
      $resultPage = $this->resultPageFactory->create();
      $resultPage->setActiveMenu('Pektsekye_OptionExtended::ox_export');
      $resultPage->getConfig()->getTitle()->prepend(__('Dependent Product Options'));
      return $resultPage;

  } 

}
