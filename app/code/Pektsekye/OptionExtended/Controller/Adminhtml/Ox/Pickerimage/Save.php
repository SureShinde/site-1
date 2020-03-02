<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Pickerimage;

class Save extends \Pektsekye\OptionExtended\Controller\Adminhtml\Ox\Pickerimage
{


  public function execute()
  {
		if ($images = $this->getRequest()->getPost('values')) {

      $pickerimage = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Pickerimage');
			  
			try {

        $pickerimage->saveImages($images);
				
        $this->messageManager->addSuccess(__('Images were successfully saved.'));				
				$this->_getSession()->setFormData(false);
      } catch (\Exception $e) {
        $this->messageManager->addError($e->getMessage());
      }				
    } 
    $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*/*/')));
  }

}
