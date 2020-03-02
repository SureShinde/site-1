<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Product\Edit;

abstract class Option extends \Magento\Backend\App\AbstractAction
{

  protected $_productFactory;
  

  public function __construct(
      \Magento\Backend\App\Action\Context $context,           
      \Magento\Catalog\Model\ProductFactory $productFactory
  ) {
      $this->_productFactory = $productFactory;             
      parent::__construct($context);
  }
       
    
  protected function _initOption()
  {
      $productId = (int) $this->getRequest()->getParam('product_id');   
      $optionId  = (int) $this->getRequest()->getParam('option_id');
      $storeId   = (int) $this->getRequest()->getParam('store');
       
      $product = $this->_productFactory->create();

      if ($productId) {
          $product->load($productId);
      } 

      if ($storeId) {
          $product->setStoreId($storeId);
      }      
      
      $this->_coreRegistry->register('product', $product);
      $this->_coreRegistry->register('option_id', $optionId);
            
      return $this;
  }    

  
  protected function _isAllowed()
  {
    return true;
  }    

}
