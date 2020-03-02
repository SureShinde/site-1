<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

abstract class Option extends \Magento\Backend\App\AbstractAction
{

  protected $_oxTemplate;
  protected $_oxTemplateOption;  
  protected $_coreOption;  
  protected $_coreRegistry; 
    

  public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Pektsekye\OptionExtended\Model\Template $template, 
      \Pektsekye\OptionExtended\Model\Template\Option $templateOption,  
      \Magento\Catalog\Model\Product\Option $coreOption,           
      \Magento\Framework\Registry $coreRegistry           
  ) {
      $this->_oxTemplate       = $template;  
      $this->_oxTemplateOption = $templateOption; 
      $this->_coreOption       = $coreOption;   
      $this->_coreRegistry     = $coreRegistry;          
      parent::__construct($context);
  }


  protected function _initOption()
  {
      $templateId = (int) $this->getRequest()->getParam('template_id');
      $optionId   = (int) $this->getRequest()->getParam('option_id');
      $storeId    = (int) $this->getRequest()->getParam('store');                  
      $option     = $this->_oxTemplateOption;
      
      if ($optionId){
        $option->load($optionId);
        $option->loadStoreFields($storeId);
        $sd = explode(',', $option->getSelectedByDefault());
        if ($option->getType() == 'radio' || $option->getType() == 'drop_down')
          $option->setSd($sd);
        elseif ($option->getType() == 'checkbox' || $option->getType() == 'multiple')
          $option->setSdMultiple($sd);                       
      } else {
        $option->setTemplateId($templateId);
      }
      
      $option->setStoreId($storeId);
            
      $this->_coreRegistry->register('current_option', $option);
      return $this;
  }
  
    
  protected function _isAllowed()
  {
    return true;
  }      
  
}
