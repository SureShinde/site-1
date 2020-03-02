<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class Delete extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {
		if ($this->getRequest()->getParam('value_id') > 0 ) {
			try {

        $this->_oxTemplateValue->getResource()->deleteValuesWithChidrenUpdate((int) $this->getRequest()->getParam('template_id'), (int) $this->getRequest()->getParam('value_id'));

				$this->messageManager->addSuccess(__('Value was successfully deleted'));
				$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));
				return;
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('_current'=>true));
				return;
			}
		}
		$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));
	}



}
