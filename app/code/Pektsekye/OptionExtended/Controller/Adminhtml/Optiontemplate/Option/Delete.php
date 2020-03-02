<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Delete extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {	
	  $optionId = $this->getRequest()->getParam('option_id');
		if (!empty($optionId)) {
			try {
				$this->_oxTemplateOption->getResource()->deleteOptionsWithChidrenUpdate((int) $this->getRequest()->getParam('template_id'), (int) $optionId);
					 
				$this->messageManager->addSuccess(__('Option was successfully deleted'));
				$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));
				return;
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('_current'=>true));
				return;
			}
		}
		$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));

	}

}
