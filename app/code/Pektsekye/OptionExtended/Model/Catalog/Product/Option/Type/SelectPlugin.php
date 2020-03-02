<?php

namespace Pektsekye\OptionExtended\Model\Catalog\Product\Option\Type;

use Magento\Framework\Exception\LocalizedException;

class SelectPlugin
{


    protected $_oxValue;  
    protected $_product; 
    protected $_imageHelper;  
    protected $_scopeConfig;
    protected $_request;        
    
    public function __construct(
        \Pektsekye\OptionExtended\Model\Value $oxValue,          
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Helper\Image $imageHelper,   
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request                       
    ) {
        $this->_oxValue = $oxValue;    
        $this->_product = $product;   
        $this->_imageHelper = $imageHelper;  
        $this->_scopeConfig = $scopeConfig;  
        $this->_request = $request;               
    }



    public function aroundValidateUserValue(\Magento\Catalog\Model\Product\Option\Type\Select $subject, \Closure $proceed, $values)
    {
    
      try {
        $proceed($values);
      } catch (LocalizedException $e) {}
      
      $subject->setIsValid(true);
            
      return $subject;  
    }



    public function afterIsCustomizedView(\Magento\Catalog\Model\Product\Option\Type\Select $subject, $result)
    {
      return true;         
    }



    public function aroundGetFormattedOptionValue(\Magento\Catalog\Model\Product\Option\Type\Select $subject, \Closure $proceed, $optionValue) { 
      $formattedValue = $proceed($optionValue);

      if ($this->canDisplayOptionImages()){
			  $option = $subject->getOption();	
			  if ($option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN 
			   && $option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO) {
				  $formattedValues = explode(', ', $formattedValue);
			    $result = '';				  
				  foreach (explode(',', $optionValue) as $k => $valueId) {
				  
				    $result .= ($k > 0 ? ', ' : '') . $formattedValues[$k];
				    if (!is_null($option->getValueById($valueId))){
				    	$image = $option->getValueById($valueId)->getImage();
				    	if (is_null($image))
    				  	$image = $this->_oxValue->load($valueId, 'option_type_id')->getImage();
					    if ($image != '')				
						    $result .= $this->makeImage($image);
						}
				  }
				  $formattedValue = $result;
			  } else {
				  if (!is_null($option->getValueById($optionValue))){			  
			      $image = $option->getValueById($optionValue)->getImage();
			    	if (is_null($image))
				    	$image = $this->_oxValue->load($optionValue, 'option_type_id')->getImage();			    
				    if ($image != '')
					    $formattedValue .= $this->makeImage($image);
					}
			  }		  		  
      }
           
      return $formattedValue;      			
    }


    public function _isSingleSelection()
    {
        $single = [
            \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN,
            \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO,
        ];
        return in_array($this->getOption()->getType(), $single);
    }


    public function makeImage($image)
    {    						
      $url = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(45,45)->setImageFile($image)->getUrl();
      return  '<img src="'.$url.'" style="vertical-align:middle;margin:5px;">';
    }


     public function canDisplayOptionImages()
    {	
      $path = $this->_request->getModuleName() .'_'. $this->_request->getControllerName() .'_'. $this->_request->getActionName(); 
     
      return $this->_scopeConfig->getValue('checkout/cart/custom_option_images',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1 && $path == 'checkout_cart_index';
    }

}

