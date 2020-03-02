<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Ox;

class Pickerimage extends \Magento\Backend\Block\Widget
{

    protected $_template = 'optionextended/ox/pickerimage.phtml';
    
    protected $_product; 
    protected $_jsonEncoder;        
    protected $_imageHelper;  
    protected $_fileSizeService; 
       
    protected $_lastImageId = 0;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context, 
        \Pektsekye\OptionExtended\Model\Pickerimage $pickerimage,          
        \Magento\Catalog\Model\Product $product, 
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,                            
        \Magento\Catalog\Helper\Image $imageHelper, 
        \Magento\Framework\File\Size $fileSize,           
        array $data = array()
    ) {    
        $this->_pickerimage = $pickerimage;
        $this->_product = $product; 
        $this->_jsonEncoder = $jsonEncoder;              
        $this->_imageHelper = $imageHelper;  
        $this->_fileSizeService = $fileSize;                
        parent::__construct($context, $data);
    }  





    public function getValues()
    {      
      $values = $this->_pickerimage->getImageData();
      foreach ($values as $k => $value){
        $values[$k]['image_url'] = '';
        if ($value['image'] != '')
          $values[$k]['image_url'] = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->keepFrame(true)->resize(40)->setImageFile($value['image'])->getUrl(); 
             
        if ($value['ox_image_id'] > $this->_lastImageId)
          $this->_lastImageId = $value['ox_image_id'];      
      } 
      
      return $values;                          
    }


    public function getImagesData()
    {    

      $values = $this->_pickerimage->getImageData();
      foreach ($values as $k => $value){
        $values[$k]['id'] = $value['ox_image_id'];
              
        $values[$k]['image_url'] = '';
        if ($value['image'] != '')
          $values[$k]['image_url'] = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->keepFrame(false)->resize(40)->setImageFile($value['image'])->getUrl(); 
             
        if ($value['ox_image_id'] > $this->_lastImageId)
          $this->_lastImageId = $value['ox_image_id'];      
      } 
      
      return $this->_jsonEncoder->encode($values);                          
    }


    public function getSaveUrl()
    {//      return $this->getUrl('.*./.*./save');
      return $this->getUrl('*/*/save');
    }


    public function getLastImageId()
    {
      return $this->_lastImageId;
    }
 
 
 
    public function getUploadUrl()
    {
      return $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload');
    }
    
    
    
    public function getFileSizeService()
    {
        return $this->_fileSizeService;
    } 
        

}
