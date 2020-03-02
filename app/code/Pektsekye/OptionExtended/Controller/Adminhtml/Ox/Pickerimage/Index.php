<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Pickerimage;

class Index extends \Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Pickerimage
{


  public function execute()
  {
      $resultPage = $this->resultPageFactory->create();
      $resultPage->setActiveMenu('Pektsekye_OptionExtended::ox_pickerimage');
      $resultPage->getConfig()->getTitle()->prepend(__('Picker Images'));
      return $resultPage;
  } 

}
