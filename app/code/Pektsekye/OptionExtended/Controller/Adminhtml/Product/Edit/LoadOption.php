<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit;

class LoadOption extends \Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit
{

  public function execute()
  {   
  
    $productId = (int) $this->getRequest()->getParam('product_id');
    $optionId = (int) $this->getRequest()->getParam('option_id');
    $storeId = (int) $this->getRequest()->getParam('store_id');
            
    $product = $this->_productFactory->create()->setStoreId($storeId)->load($productId);
    $data = $this->_optionBlock->setProduct($product)->getOptionData($optionId);    
   
    $this->getResponse()->setBody($this->_jsonEncoder->encode($data));    
  } 

}
