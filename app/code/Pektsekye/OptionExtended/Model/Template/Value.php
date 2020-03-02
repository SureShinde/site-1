<?php

namespace Pektsekye\OptionExtended\Model\Template;

class Value extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{    
   
    protected $_oxTemplate;   
    
    public function __construct(
        \Pektsekye\OptionExtended\Model\Template $template,       
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\OptionExtended\Model\ResourceModel\Template\Value $resource,                               
        \Pektsekye\OptionExtended\Model\ResourceModel\Template\Value\Collection $resourceCollection,                            
        array $data = array()
    ) {  
        $this->_oxTemplate = $template;                        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    
    public function loadStoreFields($storeId)
    {
      $row = $this->getResource()->getStoreFields((int) $this->getId(), $storeId);
      $this->setTitle($row['title']);
      $this->setStoreTitle($row['store_title']);
      $this->setPrice(number_format($row['price'], 2, null, ''));
      $this->setPriceType($row['price_type']);      
      $this->setStorePrice($row['store_price']);
      $this->setDescription($row['description']);
      $this->setStoreDescription($row['store_description']);              
    }


    public function getNextId()
    {
      return $this->getResource()->getNextId();
    }
 	     
    
    public function getValuesCsv()
    {
      return $this->getResource()->getValuesCsv();    
    }    
    

    public function getValuesTranslateCsv()
    {
      return $this->getResource()->getValuesTranslateCsv();  
    }      
    
      
    public function getIdentities()
    {      
      return $this->_oxTemplate->setId((int)$this->getTemplateId())->getIdentities();
    }     
       
}
