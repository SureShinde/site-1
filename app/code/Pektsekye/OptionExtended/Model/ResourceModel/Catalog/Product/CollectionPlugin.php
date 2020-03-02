<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Catalog\Product;

class CollectionPlugin
{

  protected $_oxTemplate;  

  public function __construct( 
      \Pektsekye\OptionExtended\Model\Template $oxTemplate         
  ) {        
      $this->_oxTemplate = $oxTemplate;                         
  } 


  public function beforeAddOptionsToResult(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject)
  {
    foreach ($subject as $item)
      $this->_oxTemplate->addOptionTemplates($item); 
      
  }


}
