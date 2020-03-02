<?php

namespace Pektsekye\OptionExtended\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_oxOption;
    protected $_oxValue;  

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Pektsekye\OptionExtended\Model\Option $oxOption,   
        \Pektsekye\OptionExtended\Model\Value $oxValue        
    ) {
        $this->_oxOption = $oxOption; 
        $this->_oxValue  = $oxValue;     
        parent::__construct($context);
    }




    public function getHiddenRequiredOptions($product, $requestOptions)
    {
					
			$children = array();		

      foreach ($product->getOptions() as $option){
        if (!is_null($option->getLayout())){
          
          if (!is_null($option->getRowId()))					
            $option_id_by_row_id[(int) $option->getRowId()] = $option->getOptionId();
        
          if (!is_null($option->getValues())){			  			  
            foreach ($option->getValues() as $value) {
              $valueId = $value->getOptionTypeId();	                 			      		
              $value_id_by_row_id[$value->getRowId()] = $valueId;                      
              $children[$valueId] = explode(',', $value->getChildren());							
            }
          }		
        }		  						
      }			
    
    
      $options = $this->_oxOption->getCollection()		
        ->addFieldToFilter('product_id', $product->getId());		
      foreach ($options as $option){
        if (!is_null($option->getRowId()))					
          $option_id_by_row_id[(int) $option->getRowId()] = $option->getOptionId();		                            
      }	   
    
      $values = $this->_oxValue->getCollection()		
        ->addFieldToFilter('product_id', $product->getId());	
      foreach ($values as $value) {
        $valueId = $value->getOptionTypeId();							
        $value_id_by_row_id[$value->getRowId()] = $valueId;           
        $children[$valueId] = explode(',', $value->getChildren());									
      }						


      $oIdByVId = array();			
      foreach ($product->getOptions() as $option){
        if ($values = $option->getValues()){
          foreach ($values as $vId => $v)
            $oIdByVId[$vId] = $option->getId();
        }		  
      }
			
			$cOIdsByVId = array();	
			$cVIdsByVId = array();			
			$parentVIdByOId  = array();			
			foreach ($children as $valueId => $value){
        foreach ($value as $rId){
          if (isset($option_id_by_row_id[$rId])){
            $oId = (int) $option_id_by_row_id[$rId];
            $cOIdsByVId[$valueId][] = $oId;
            $parentVIdByOId[$oId] = $valueId;
          } elseif(isset($value_id_by_row_id[$rId])){
            $vId = (int) $value_id_by_row_id[$rId];		
            $cVIdsByVId[$valueId][] = $vId;
            $parentVIdByOId[$oIdByVId[$vId]] = $valueId;														
          }	
        }
			}						
	
      $visibleOIds = array();	
      foreach ($requestOptions as $v){
        $vIds = is_array($v) ? $v : array($v);
        foreach ($vIds as $vId){
          if (isset($cOIdsByVId[$vId]))
            foreach ($cOIdsByVId[$vId] as $oId)          
              $visibleOIds[$oId] = 1;
          if (isset($cVIdsByVId[$vId]))
            foreach ($cVIdsByVId[$vId] as $id)
              $visibleOIds[$oIdByVId[$id]] = 1;                      
        }                 	  
      }
      	
      $hiddenOIds	= array();
      foreach ($product->getOptions() as $option){
        $oId = $option->getId();
        if ($option->getIsRequire() && isset($parentVIdByOId[$oId]) && !isset($visibleOIds[$oId])){
          $hiddenOIds[$oId]	= 1;
        }		  
      }	
	
			return $hiddenOIds;			
   
    }

}


