<?php

namespace Pektsekye\OptionExtended\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class OptionSaveAfter implements ObserverInterface
{

  protected $_mediaConfig;
  protected $_mediaDirectory;  
  protected $_objectManager;
  
  protected $_previousImages;  


  public function __construct( 
      \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
      \Magento\Framework\Filesystem $filesystem,
      \Magento\Framework\ObjectManagerInterface $objectManager           
  ) {        
      $this->_mediaConfig     = $mediaConfig; 
      $this->_mediaDirectory  = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);      
      $this->_objectManager   = $objectManager;                          
  } 
 

  public function execute(\Magento\Framework\Event\Observer $observer)
  {

		$object = $observer->getEvent()->getObject();
		$resource_name = $object->getResourceName();

		if ($resource_name == 'Magento\Catalog\Model\ResourceModel\Product\Option'){

			$model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Option');

			$collection = $model->getCollection()->addFieldToFilter('option_id', $object->getId());
			if ($item = $collection->getFirstItem())	
				$model->setId($item['ox_option_id']);
	
			$code = $object->getCode() != '' ? $object->getCode() : 'opt-'. $object->getProductId() .'-'. $object->getId();
			
			$model->setStoreId($object->getStoreId());
			$model->setOptionId($object->getId());	
			$model->setProductId($object->getProductId());
			$model->setScope($object->getScope());			
			$model->setRowId($object->getRowId());
			$model->setNote($object->getNote());			
			$model->setLayout($object->getLayout());
			$model->setPopup((int) $object->getPopup());
			$model->setCode($code);			
			$model->setSelectedByDefault((string) $object->getSelectedByDefault());	
			$model->save();	
		
		} elseif ($resource_name == 'Magento\Catalog\Model\ResourceModel\Product\Option\Value'){// && $object->getRowId()

			$ox_value_id = null;
			$model = $this->_objectManager->create('Pektsekye\OptionExtended\Model\Value');
			$collection = $model->getCollection()->addFieldToFilter('option_type_id', $object->getId());
			if ($item = $collection->getFirstItem())
				$ox_value_id = $item['ox_value_id'];
				
			$model->setStoreId($object->getStoreId());
			$model->setId($ox_value_id);	
			$model->setOptionTypeId($object->getId());			
			if ($object->getProduct())				
				$model->setProductId($object->getProduct()->getId());		
			else 
				$model->setProductId($object->getOption()->getProductId());
			$model->setScope($object->getScope());				
			$model->setRowId((int) $object->getRowId());
			$model->setChildren((string) $object->getChildren());
							
			$image = $object->getImage();
			if (!empty($image)) {
				$image = $this->_moveImageFromTmp($image);
			}	else {
			  $image = $object->getImageSavedAs();
			}			
			if (!empty($image) || $object->getDeleteImage() == 1){
				$model->setImage($image);
			}			
			$model->setDescription((string) $object->getDescription());		
			$model->save();

		}

    return $this;
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

    $tmpFile = $this->_mediaConfig->getTmpMediaPath($file);
     
    if (isset($this->_previousImages[$tmpFile])){
      $fileNameToSave = $this->_previousImages[$tmpFile]; 
    } else { 
    
      $this->_mediaDirectory->renameFile($tmpFile, $this->_mediaConfig->getMediaPath($destFile));      
            
      $fileNameToSave = str_replace('\\', '/', $destFile);
      $this->_previousImages[$tmpFile] = $fileNameToSave;      
    }
      
    return $fileNameToSave;
  }  
  
  
  
}
