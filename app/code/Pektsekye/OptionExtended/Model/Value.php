<?php

namespace Pektsekye\OptionExtended\Model;

class Value extends \Magento\Framework\Model\AbstractModel
{ 

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\OptionExtended\Model\ResourceModel\Value $resource,                               
        \Pektsekye\OptionExtended\Model\ResourceModel\Value\Collection $resourceCollection,                              
        array $data = array()
    ) {                       
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

  
    public function getValuesCsv()
    {
      return $this->getResource()->getValuesCsv();    
    }    
    

    public function getValuesTranslateCsv()
    {
      return $this->getResource()->getValuesTranslateCsv();  
    }  	
	 
}