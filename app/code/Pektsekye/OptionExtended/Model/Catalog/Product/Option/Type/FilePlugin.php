<?php

namespace Pektsekye\OptionExtended\Model\Catalog\Product\Option\Type;

use Magento\Framework\Exception\LocalizedException;

class FilePlugin
{

    protected $_httpFactory;
    
    public function __construct(
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory
    ) {
        $this->_httpFactory = $httpFactory;
    }
    
        
    public function aroundValidateUserValue(\Magento\Catalog\Model\Product\Option\Type\File $subject, \Closure $proceed, $values)
    {
      $option = $subject->getOption();
    
      $buyRequest = $subject->getRequest();
      $params = $buyRequest->getData('_processing_params');      
      if (!$params instanceof \Magento\Framework\DataObject) {
        $params = new \Magento\Framework\DataObject();
      }             

      $upload = $this->_httpFactory->create();
      $file = $params->getFilesPrefix() . 'options_' . $option->getId() . '_file';              
    
      if ($upload->isUploaded($file)){//validate only visible option (not hidden with dependecy)
        $proceed($values);      
      } else {
        try {
          $proceed($values);
        } catch (LocalizedException $e) {} 
        $subject->setIsValid(true);     
      }          
                    
      return $subject;  
    }


}

