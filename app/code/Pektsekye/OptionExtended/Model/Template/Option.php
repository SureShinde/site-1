<?php

namespace Pektsekye\OptionExtended\Model\Template;

class Option extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{    
    
    protected $_oxTemplate;
  
    public function __construct(   
        \Pektsekye\OptionExtended\Model\Template $template,     
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\OptionExtended\Model\ResourceModel\Template\Option $resource,                               
        \Pektsekye\OptionExtended\Model\ResourceModel\Template\Option\Collection $resourceCollection,                                
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
      $this->setNote($row['note']);
      $this->setStoreNote($row['store_note']);  

      return $this;            
    }


    public function getLastRowId()
    {
      return $this->getResource()->getLastRowId((int) $this->getTemplateId());
    }


    public function getNextId()
    {
      return $this->getResource()->getNextId();
    }
    
    
    public function getValueTitles()
    {
      return $this->getResource()->getValueTitles((int) $this->getId());
    }
    
    
    public function deleteValues()
    {
      $this->getResource()->deleteValuesWithChidrenUpdate((int) $this->getTemplateId(), (int) $this->getId());
    }   
    
    
    public function deletePrice()
    {
      $this->getResource()->deletePrice((int) $this->getId());
    }   


    public function getOptionsCsv()
    {
      return $this->getResource()->getOptionsCsv();    
    }       


    public function getOptionsTranslateCsv()
    {
      return $this->getResource()->getOptionsTranslateCsv();  
    }      
 
 
    public function getIdentities()
    {      
      return $this->_oxTemplate->setId((int)$this->getTemplateId())->getIdentities();
    }         
       
}
