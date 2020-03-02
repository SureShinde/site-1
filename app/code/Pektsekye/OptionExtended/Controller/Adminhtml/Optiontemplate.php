<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml;

abstract class Optiontemplate extends \Magento\Backend\App\AbstractAction
{



  protected $_coreRegistry; 
  protected $_oxTemplate;
  protected $_productResource;
  protected $_jsonEncoder;  
  

  public function __construct(
      \Magento\Backend\App\Action\Context $context,
      \Pektsekye\OptionExtended\Model\Template $template, 
      \Magento\Framework\Registry $coreRegistry,            
      \Magento\Catalog\Model\ResourceModel\Product $productResource,
      \Magento\Framework\Json\EncoderInterface $jsonEncoder      
  ) {
      $this->_oxTemplate = $template;  
      $this->_coreRegistry    = $coreRegistry;       
      $this->_productResource = $productResource;
      $this->_jsonEncoder = $jsonEncoder;            
      parent::__construct($context);
  }
   
    
    
  protected function _initTemplate()
  {
      $templateId = (int) $this->getRequest()->getParam('template_id');    
      $template = $this->_oxTemplate;
      
      if ($templateId){
        $template->load($templateId);
        $productIds = $template->getResource()->getProductIds($templateId);
        $template->setProductIds($productIds);
        $template->setOldProductIds($productIds);        
        $template->setProductIdsString(implode(',', $productIds));
      } else {
        $template->setIsActive(1);
      }
      
      $this->_coreRegistry->register('current_template', $template);
      return $this;
  }    
    
  
  protected function _isAllowed()
  {
      return $this->_authorization->isAllowed('Pektsekye_OptionExtended::templates');
  }     

}
