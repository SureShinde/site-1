<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Product;

abstract class Edit extends \Magento\Backend\App\AbstractAction
{
  protected $_optionBlock;
  protected $_productFactory;
  protected $_coreRegistry;    
  protected $_jsonEncoder;  


  public function __construct(
      \Magento\Backend\App\Action\Context $context,     
      \Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options\Option $optionBlock,           
      \Magento\Catalog\Model\ProductFactory $productFactory,
      \Magento\Framework\Registry $coreRegistry,      
      \Magento\Framework\Json\EncoderInterface $jsonEncoder       
  ) {
      $this->_optionBlock    = $optionBlock;
      $this->_productFactory = $productFactory;
      $this->_coreRegistry   = $coreRegistry;      
      $this->_jsonEncoder    = $jsonEncoder;                   
      parent::__construct($context);
  }


  protected function _initProduct()
  {
      $productId  = (int) $this->getRequest()->getParam('id');
      $product    = $this->_productFactory->create()
          ->setStoreId($this->getRequest()->getParam('store', 0));

      if ($productId)
        $product->load($productId);

      $this->_coreRegistry->register('product', $product);
  } 


  protected function _isAllowed()
  {
    return true;
  } 
   
}
