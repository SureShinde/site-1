<?php

namespace Pektsekye\OptionExtended\Model;

class Option extends \Magento\Framework\Model\AbstractModel
{        
    
    public function __construct(     
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\OptionExtended\Model\ResourceModel\Option $resource,                               
        \Pektsekye\OptionExtended\Model\ResourceModel\Option\Collection $resourceCollection,                              
        array $data = array()
    ) {                     
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    public function getOptionsCsv()
    {
      return $this->getResource()->getOptionsCsv();    
    }    
    

    public function getOptionsTranslateCsv()
    {
      return $this->getResource()->getOptionsTranslateCsv();  
    }  
    	
}