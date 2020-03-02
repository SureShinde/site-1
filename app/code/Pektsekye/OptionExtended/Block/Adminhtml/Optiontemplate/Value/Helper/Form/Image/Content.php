<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Image;

class Content extends \Magento\Backend\Block\Widget
{

    protected $_template = 'optiontemplate/helper/image.phtml';
    
    protected $_product; 
    protected $_coreRegistry = null;        
    protected $_imageHelper;  
    protected $_fileSizeService;    


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context, 
        \Magento\Catalog\Model\Product $product,                
        \Magento\Framework\Registry $registry,        
        \Magento\Catalog\Helper\Image $imageHelper, 
        \Magento\Framework\File\Size $fileSize,           
        array $data = array()
    ) {    
        $this->_product = $product;      
        $this->_coreRegistry = $registry;     
        $this->_imageHelper = $imageHelper;  
        $this->_fileSizeService = $fileSize;                
        parent::__construct($context, $data);
    }  


    public function getUploadUrl()
    {
      return $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload');
    }


    public function getImage()
    {
      $image = $this->_coreRegistry->registry('current_value')->getImage();
      if (!empty($image)){
        $image = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
      }
      return $image;             
    }


    public function getFileSizeService()
    {
        return $this->_fileSizeService;
    }

}
