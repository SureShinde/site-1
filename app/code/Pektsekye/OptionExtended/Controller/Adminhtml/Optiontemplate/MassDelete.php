<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class MassDelete extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
      $ids = $this->getRequest()->getParam('ids');
      if(!is_array($ids)) {
        $this->messageManager->addError(__('Please select item(s)'));
      } else {
          try {
              foreach ($ids as $id) {
                $this->_oxTemplate->setId($id)->delete();                    
              }
              $this->messageManager->addSuccess(__('Total of %1 record(s) were successfully deleted', count($ids)));
          } catch (\Exception $e) {
              $this->messageManager->addError($e->getMessage());
          }
      }
      $this->_redirect('*/*/');
  }

}
