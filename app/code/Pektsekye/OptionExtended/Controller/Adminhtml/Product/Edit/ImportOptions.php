<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit;

class ImportOptions extends \Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit
{

  public function execute()
  {   
  
    $productId = (int) $this->getRequest()->getParam('product_id');
    $lastOptionId = (int) $this->getRequest()->getParam('last_option_id');    
    $lastValueId = (int) $this->getRequest()->getParam('last_value_id'); 
    $lastRowId = (int) $this->getRequest()->getParam('last_row_id'); 
    $lastSortOrder = (int) $this->getRequest()->getParam('last_sort_order'); 
              
    $product = $this->_productFactory->create()->setStoreId(0)->load($productId);
    $data = $this->_optionBlock->setProduct($product)->getImportFromProductData($lastOptionId, $lastValueId, $lastRowId, $lastSortOrder);    
   
    $this->getResponse()->setBody($this->_jsonEncoder->encode($data));    
  } 

}
