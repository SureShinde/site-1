<?php

namespace Pektsekye\OptionExtended\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfter implements ObserverInterface
{

  protected $_oxTemplate;    
  protected $_request;
  

  public function __construct( 
      \Pektsekye\OptionExtended\Model\Template $oxTemplate,
      \Magento\Framework\App\RequestInterface $request                   
  ) {        
      $this->_oxTemplate = $oxTemplate; 
      $this->_request    = $request;                             
  } 
 


  public function execute(\Magento\Framework\Event\Observer $observer)
  {
  
		$product   = $observer->getEvent()->getProduct();
		$idsString = $product->getOptionextendedTemplateIds();	
		
    $productRequest = $this->_request->getParam('product');
    
		if (!is_null($idsString) || isset($productRequest['optionextended_template_ids'])){
		  $templateIds = $idsString != '' ? explode(',', $idsString) : array();
      $this->_oxTemplate->getResource()->updateProductTemplates((int) $product->getId(), $templateIds);								
    }

    return $this;
  }
  
  
}
