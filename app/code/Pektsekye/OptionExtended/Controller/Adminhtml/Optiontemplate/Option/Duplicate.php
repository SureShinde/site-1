<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Duplicate extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
	  $optionId = $this->getRequest()->getParam('option_id');
		if (!empty($optionId)) {
			try {
				$newOptionId = $this->_oxTemplateOption->getResource()->duplicate((int) $optionId);					 
				$this->messageManager->addSuccess(__('Option was successfully duplicated'));
			} catch (\Exception $e) {
				$newOptionId = $optionId;
				$this->messageManager->addError($e->getMessage());
			}
		}
		
    $this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $newOptionId, 'store'=> (int)$this->getRequest()->getParam('store')));     
	}

}
