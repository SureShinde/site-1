<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Doimport extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
	  $productId = $this->getRequest()->getPost('product_id');
		if (!empty($productId)) {
			try {
				$this->_oxTemplateOption->getResource()->importOptionsFromProduct((int) $this->getRequest()->getParam('template_id'), (int) $productId);
					 
				$this->messageManager->addSuccess(__('Options were successfully imported'));
				$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));
				return;
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->_redirect('*/*/import', array('template_id' => $this->getRequest()->getParam('template_id')));
				return;				
			}
		}
    $this->_redirect('*/*/import', array('template_id' => $this->getRequest()->getParam('template_id'))); 
	}

}
