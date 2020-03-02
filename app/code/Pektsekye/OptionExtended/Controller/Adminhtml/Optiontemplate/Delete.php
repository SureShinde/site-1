<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Delete extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
	  $templateId = $this->getRequest()->getParam('template_id');
		if ($templateId > 0) {
			try {
				$this->_oxTemplate->setId($templateId)
				  ->delete();
					 
				$this->messageManager->addSuccess(__('Template was successfully deleted'));
				$this->_redirect('*/*/');
				return;				
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('_current'=>true));
				return;
			}
		}
		$this->_redirect('*/*/');
	}

}
