<?php

namespace Pektsekye\OptionExtended\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddOptionTemplatesToProduct implements ObserverInterface
{

  protected $_oxTemplate;  

  public function __construct( 
      \Pektsekye\OptionExtended\Model\Template $oxTemplate         
  ) {        
      $this->_oxTemplate = $oxTemplate;                         
  } 


  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $product = $observer->getEvent()->getProduct();	
    $this->_oxTemplate->addOptionTemplates($product);

    return $this;
  }

  
}
