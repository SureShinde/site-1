<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class MassStatus extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
      $ids = $this->getRequest()->getParam('ids');
      if (!is_array($ids)) {
        $this->messageManager->addError(__('Please select item(s)'));
      } else {
          try {
              foreach ($ids as $id) {
                $this->_oxTemplate->load($id)
                  ->setIsActive($this->getRequest()->getParam('status'))
                  ->save();                    
              }
              $this->messageManager->addSuccess(__('Total of %1 record(s) have been updated.', count($ids)));
          } catch (\Exception $e) {
              $this->messageManager->addError($e->getMessage());
          }
      }
      $this->_redirect('*/*/');
  } 

}
