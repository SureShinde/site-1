<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class Save extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
	
		if ($post = $this->getRequest()->getPost()) {

      $this->_initTemplate();
      $template = $this->_coreRegistry->registry('current_template');
			$template->setTitle($post['template_title']);
			$template->setCode($post['template_code']);		
		  $template->setIsActive($post['is_active']);
		  $template->setProductIds(explode(',', $post['product_ids_string']));

			try {

        $template->save();
				
				$this->messageManager->addSuccess(__('Template was successfully saved'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect($this->getUrl('*/*/edit', array('template_id' => $template->getId(), '_current' => true)));
					return;
				}
				$this->_redirect($this->getUrl('*/*/'));
				return;
      } catch (\Exception $e) {
        $this->messageManager->addError($e->getMessage());
        $this->_redirect($this->getUrl('*/*/edit', array('_current'=>true)));
        return;
      }
    } 
    $this->messageManager->addError(__('Unable to find template to save'));
    $this->_redirect($this->getUrl('*/*/'));
	}

}
