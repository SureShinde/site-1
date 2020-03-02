<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option;

class Save extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Option
{


  public function execute()
  {
	
		if ($post = $this->getRequest()->getPost()) {

      $this->_initOption();
      
      $option   = $this->_coreRegistry->registry('current_option');   
      $group    = $this->_coreOption->getGroupByType($post['type']);
      $oldGroup = $this->_coreOption->getGroupByType($option->getType()); 
         
      if (!is_null($option->getId()) && $group != $oldGroup){
        if ($group != 'select')
			    $option->deleteValues();
			  else
          $option->deletePrice();			              
      }
      
      $rowId = null;
      if ($group != 'select'){
        if ($post['row_id'] != '')
          $rowId = $post['row_id'];
        else 
          $rowId = (int) $option->getLastRowId() + 1;
      }		    
	    
	    $option->setRowId($rowId);
			if (isset($post['title']))      
			  $option->setTitle($post['title']);
			$option->setType($post['type']);
			$option->setIsRequire($post['is_require']);
			$option->setSortOrder($post['sort_order']);
			$code = $post['code'] != '' ? $post['code'] : (is_null($option->getId()) ? 'opt-'. $option->getTemplateId() .'-'. $option->getNextId() : $option->getCode($code));			
			$option->setCode($code);
			if (isset($post['note']))
			  $option->setNote($post['note']);			
		  $option->setLayout($post['layout']);
		  $option->setPopup(isset($post['popup']) ? 1 : 0);
		        
      if ($post['type'] == 'radio' || $post['type'] == 'drop_down'){
        $sd = $post['sd']; 
      } else { 
        $sd = '';                   
        if (isset($post['sd_multiple'])){
          if ($post['sd_multiple'][0] == '-1')
            unset($post['sd_multiple'][0]);
          $sd = implode(',', $post['sd_multiple']);
        }
      }
      $option->setSelectedByDefault($sd);
    	        			  			  
			if (isset($post['price']))
        $option->setPrice($post['price']);
      $option->setPriceType($post['price_type']);
      $option->setSku($post['sku']);        
      $option->setMaxCharacters($post['max_characters']);
      $option->setFileExtension($post['file_extension']);
      $option->setImageSizeX($post['image_size_x']);
      $option->setImageSizeY($post['image_size_y']);	      																											

      if ($option->getStoreId() != 0){
			  if (isset($post['title_use_default']))      			
			    $option->setTitleUseDefault(1);
			  if (isset($post['note_use_default']))  			    
			    $option->setNoteUseDefault(1);
			  if (isset($post['price_use_default'])) 
			  	$option->setPriceUseDefault(1);
			}
		  
			try {

        $option->save();

	
				$this->messageManager->addSuccess(__('Option was successfully saved'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $option->getId(), 'store'=> (int)$this->getRequest()->getParam('store')));
					return;
				}
				$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));
				return;
      } catch (\Exception $e) {
        $this->messageManager->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $option->getId(), 'store'=> (int)$this->getRequest()->getParam('store')));
        return;
      }
    }
    
    $this->messageManager->addError(__('Unable to find option to save'));
    $this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id')));
	}

}
