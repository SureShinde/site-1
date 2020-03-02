<?php

namespace Pektsekye\OptionExtended\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveBefore implements ObserverInterface
{
 

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
		$product = $observer->getEvent()->getProduct();
		
    $hasOptions = false;
    $hasRequiredOptions = false;
        		
    foreach ((array) $product->getOptions() as $option) {
      if ($option instanceof \Magento\Catalog\Api\Data\ProductCustomOptionInterface) {
        $option = $option->getData();
      }
      if (!isset($option['is_delete']) || $option['is_delete'] != '1') {
        $hasOptions = true;
        if (isset($option['is_require']) && $option['is_require'] == '1') {
          $hasRequiredOptions = true;
          break;
        }                  
      }
    }	

    $product->setHasOptions($hasOptions);
    $product->setRequiredOptions($hasRequiredOptions);             	         	         			

    return $this;
  }
  
  
}
