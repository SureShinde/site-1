<?php

namespace Pektsekye\OptionExtended\Model\Catalog\Product\Type;

class Plugin
{

    public function aroundCheckProductBuyState(\Magento\Catalog\Model\Product\Type\AbstractType $subject, \Closure $proceed, $product)
    {    
      return $subject;  
    }


/*
    public function beforeProcessConfiguration(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $buyRequest, $product, $processMode = null)
    {
        foreach ($product->getOptions() as $option){
          $option->setIsRequire(false);
        }   
    }



    public function beforePrepareForCartAdvanced(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $buyRequest, $product, $processMode = null)
    {
        foreach ($product->getOptions() as $option){
          $option->setIsRequire(false);
        }  
         
    }



    public function beforeCheckProductBuyState(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $product)
    {
        foreach ($product->getOptions() as $option){
          $option->setIsRequire(false);
        } 
        
    }
*/

}
