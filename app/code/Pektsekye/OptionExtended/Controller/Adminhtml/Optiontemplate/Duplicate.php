<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Duplicate extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {

	  $templateId = $this->getRequest()->getParam('template_id');
		if (!empty($templateId)) {
			try {
				$newTemplateId = $this->_oxTemplate->getResource()->duplicate((int) $templateId);					 
				$this->messageManager->addSuccess(__('Template was successfully duplicated'));
			} catch (\Exception $e) {
			  $newTemplateId = $templateId;
				$this->messageManager->addError($e->getMessage());
			}
		}
		
    $this->_redirect('*/*/edit', array('template_id' => $newTemplateId));     
	}

}
