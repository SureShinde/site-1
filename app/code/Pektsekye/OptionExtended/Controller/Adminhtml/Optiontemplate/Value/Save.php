<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class Save extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {

		if ($post = $this->getRequest()->getPost()) {
		

      $this->_initValue();
      $value = $this->_coreRegistry->registry('current_value');      

	    if ($post['row_id'] != '')
	      $rowId = $post['row_id'];
	    else 
	      $rowId = (int) $this->_oxTemplateOption->getResource()->getLastRowId($value->getTemplateId()) + 1;		    
		    
		  $value->setRowId($rowId);   
			if (isset($post['title']))   		            
			  $value->setTitle($post['title']);	
			if (isset($post['price']))			  		
        $value->setPrice($post['price']);
      $value->setPriceType($post['price_type']);
      $value->setSku($post['sku']); 
			$value->setSortOrder($post['sort_order']);
			$value->setChildren($post['children']);

			$image = $post['image_file_name'];
			if (!empty($image)) {
				$image = $this->_moveImageFromTmp($image);
			}				
			if (!empty($image) || $post['delete_image'] == 1){
				$value->setImage($image);
			}	
			  
			if (isset($post['description']))   			  			
			  $value->setDescription($post['description']);
																						

      if ($value->getStoreId() != 0){			
			  if (isset($post['title_use_default']))           
			    $value->setTitleUseDefault(1);
			  if (isset($post['description_use_default']))   			    
			    $value->setDescriptionUseDefault(1);
			  if (isset($post['price_use_default'])) 
			  	$value->setPriceUseDefault(1);
			}
		  
			try {

        $value->save();

	
				$this->messageManager->addSuccess(__('Value was successfully saved'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id'), 'value_id' => $value->getId(), 'store'=> (int)$this->getRequest()->getParam('store')));
					return;
				}
				$this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));
				return;
      } catch (\Exception $e) {
        $this->messageManager->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id'), 'value_id' => $value->getId(), 'store'=> (int)$this->getRequest()->getParam('store')));
        return;
      }
    }
    
    $this->messageManager->addError(__('Unable to find value to save'));
    $this->_redirect('*/*/', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));
	}



  protected function _moveImageFromTmp($file)
  {
      if (strrpos($file, '.tmp') == strlen($file) - 4) {
          $file = substr($file, 0, strlen($file) - 4);
      }

      $destFile = dirname(
          $file
      ) . '/' . \Magento\MediaStorage\Model\File\Uploader::getNewFileName(
          $this->_mediaDirectory->getAbsolutePath($this->_mediaConfig->getMediaPath($file))
      );
      
      $this->_mediaDirectory->renameFile(
        $this->_mediaConfig->getTmpMediaPath($file),
        $this->_mediaConfig->getMediaPath($destFile)
      );	
	
      return str_replace('\\', '/', $destFile);
  }
  
  
  
}
