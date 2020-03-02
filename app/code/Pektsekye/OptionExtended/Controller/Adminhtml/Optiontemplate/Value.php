<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

abstract class Value extends \Magento\Backend\App\AbstractAction
{


  protected $_coreRegistry; 
  protected $_oxTemplate;
  protected $_oxTemplateOption;  
  protected $_oxTemplateValue;   
  protected $_mediaConfig;
  protected $_mediaDirectory;  
  
  
  /**
   * @param \Magento\Backend\App\Action\Context $context
   * @param \Magento\App\Response\Http\FileFactory $fileFactory
   */
  public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Pektsekye\OptionExtended\Model\Template $template, 
      \Pektsekye\OptionExtended\Model\Template\Option $templateOption,  
      \Pektsekye\OptionExtended\Model\Template\Value $templateValue,           
      \Magento\Framework\Registry $coreRegistry,        
      \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
      \Magento\Framework\Filesystem $filesystem
  ) {
      $this->_oxTemplate       = $template;  
      $this->_oxTemplateOption = $templateOption;     
      $this->_oxTemplateValue  = $templateValue;         
      $this->_coreRegistry     = $coreRegistry;       
      $this->_mediaConfig      = $mediaConfig; 
      $this->_mediaDirectory   = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA); 
      parent::__construct($context);
  }
  

  protected function _initValue()
  {
      $templateId = (int) $this->getRequest()->getParam('template_id');  
      $optionId   = (int) $this->getRequest()->getParam('option_id');
      $valueId    = (int) $this->getRequest()->getParam('value_id');
      $storeId    = (int) $this->getRequest()->getParam('store');                  
      $value      = $this->_oxTemplateValue;
      
      if ($valueId){
        $value->load($valueId);
        $value->loadStoreFields($storeId);             
      } else {
        $value->setOptionId($optionId);        
      }
      
      $value->setTemplateId($templateId);      
      $value->setStoreId($storeId);
            
      $this->_coreRegistry->register('current_value', $value);
      return $this;
  }
  	

  protected function _isAllowed()
  {
    return true;
  }    
    	
}
