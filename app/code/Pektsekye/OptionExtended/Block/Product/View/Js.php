<?php

namespace Pektsekye\OptionExtended\Block\Product\View;

class Js extends \Magento\Framework\View\Element\Template
{
    protected $_pickerimage;
    protected $_oxOption;
    protected $_oxValue;    
    protected $_coreRegistry;    
    protected $_jsonEncoder;
    protected $_catalogData;    
    protected $_product; 
    protected $_imageHelper;        
    protected $_mediaConfig;
              
	  protected $config = array();
	  protected $thumbnailDirUrl = '';		
	  protected $pickerImageDirUrl = '';    
	  protected $hoverImageDirUrl = '';
	            
	                 
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Pektsekye\OptionExtended\Model\Pickerimage $pickerimage,        
        \Pektsekye\OptionExtended\Model\Option $oxOption,   
        \Pektsekye\OptionExtended\Model\Value $oxValue,         
        \Magento\Framework\Registry $coreRegistry, 
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,  
        \Magento\Catalog\Helper\Data $catalogData,  
        \Magento\Catalog\Helper\Image $imageHelper, 
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,                                                                               
        array $data = array()
    ) {
        $this->_pickerimage    = $pickerimage;     
        $this->_oxOption       = $oxOption; 
        $this->_oxValue        = $oxValue;             
        $this->_coreRegistry   = $coreRegistry;   
        $this->_jsonEncoder    = $jsonEncoder; 
        $this->_catalogData    = $catalogData;   
        $this->_imageHelper    = $imageHelper; 
        $this->_mediaConfig = $mediaConfig;      
                                                                            
        parent::__construct($context, $data);
    } 
    
    protected function _beforeToHtml()
    {	
   
      $children = array();		
      $sd = array();	
      $configValues = array();
      $inPreConfigured = $this->getProduct()->hasPreconfiguredValues();
      $storeId = $this->_storeManager->getStore()->getId();						
      $product_id = $this->getProduct()->getId();


      $options = $this->getProduct()->getOptions();


      $allPickerImages = array();    
      $rows = $this->_pickerimage->getImageData();
      foreach ($rows as $r){
        $title = strtolower(preg_replace('/[\s\W]+/', '', $r['title']));
        $allPickerImages[$title] = $r['image'];
      }    
      
      $oxPickerOption = array();
      $oxOptions = $this->_oxOption->getCollection()->addFieldToFilter('product_id', $product_id);		
      foreach ($oxOptions as $option){
        if ($option->getLayout() == 'picker' || $option->getLayout() == 'pickerswap')
          $oxPickerOption[$option->getOptionId()] = 1;
      }	

      $pickerImages = array();
      foreach ($options as $option){		
        if ($option->getLayout() == 'picker' || $option->getLayout() == 'pickerswap' || isset($oxPickerOption[$option->getOptionId()])){
          foreach ($option->getValues() as $value){
            $title = strtolower(preg_replace('/[\s\W]+/', '', $value->getTitle()));
            if (isset($allPickerImages[$title]))
              $pickerImages[$value->getOptionTypeId()] = $allPickerImages[$title];
          }
        }
      }	


      foreach ($options as $option){
      
        $id = (int) $option->getOptionId(); 
        
        $this->config[0][$id] = array('', 'above', 0, array());
        
        foreach ((array)$option->getValues() as $value){
          $valueId = (int) $value->getOptionTypeId();        
          $this->config[1][$valueId] = array('', '', array(), array(), '');
        }                
        
        if (!is_null($option->getLayout())){
          
          if (!is_null($option->getRowId()))					
            $option_id_by_row_id[$option->getTemplateId()][(int) $option->getRowId()] = $id;

          $this->config[0][$id][0] = $option->getNote() != '' ? $this->_catalogData->getPageTemplateProcessor()->filter($option->getNote()) : '';	
          $this->config[0][$id][1] = $option->getLayout();					
          $this->config[0][$id][2] = (int) $option->getPopup();	
        
          if ($inPreConfigured){
            $configValues[$id] = array();			
            if (is_null($option->getRowId())){
              $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $id);	
              if (!is_null($configValue))
                $configValues[$id] = (array) $configValue;					
            }
          } else { 
            $sd[$option->getTemplateId()][$id] = explode(',', $option->getSelectedByDefault());
          }	

        
          if (!is_null($option->getValues())){			  			  
            foreach ($option->getValues() as $value) {
              $valueId = (int) $value->getOptionTypeId();
            
              $rowId = (int) $value->getRowId();				      		
              $valueId_by_row_id[$value->getTemplateId()][$rowId] = $valueId;
                        
              $children[$value->getTemplateId()][$valueId] = explode(',', $value->getChildren());
              	
              $pickerImage = isset($pickerImages[$valueId]) ? $pickerImages[$valueId] : '';
            
              $image = '';
              if ($pickerImage != '') {
                $image = $pickerImage;
              } else {
                $image = $value->getImage();
              } 

              $largeImage = '';
              if ($value->getImage() != '') {
                $largeImage = $value->getImage();
              } else {
                $largeImage = $pickerImage; 
              } 			      

              $this->prepareImages($image, $largeImage);	
                                
              $this->config[1][$valueId][0] = $image;						
              $this->config[1][$valueId][1] = $value->getDescription() != '' ? $this->_catalogData->getPageTemplateProcessor()->filter($value->getDescription()) : '';	
              $this->config[1][$valueId][2] = array();	
              $this->config[1][$valueId][3] = array();
              $this->config[1][$valueId][4] = $largeImage;              
			
            }
          }		
        }  						
      }

        
      $options = $this->_oxOption->getCollection()
        ->joinNotes($storeId)				
        ->addFieldToFilter('product_id', $product_id);		
      foreach ($options as $option){
        $id = (int) $option->getOptionId();
    
        if (!is_null($option->getRowId()))					
          $option_id_by_row_id['orig'][(int) $option->getRowId()] = $id;
  
        $this->config[0][$id][0] = $option->getNote() != '' ? $this->_catalogData->getPageTemplateProcessor()->filter($option->getNote()) : '';	
        $this->config[0][$id][1] = $option->getLayout();					
        $this->config[0][$id][2] = (int) $option->getPopup();	
      
        if ($inPreConfigured){
          $configValues[$id] = array();			
          if (is_null($option->getRowId())){
            $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $id);	
            if (!is_null($configValue))
              $configValues[$id] = (array) $configValue;					
          }
        } else { 
          $sd['orig'][$id] = explode(',', $option->getSelectedByDefault());
        }			
                            
      }	
    
      $values = $this->_oxValue->getCollection()
        ->joinDescriptions($storeId)				
        ->addFieldToFilter('product_id', $product_id);	
      foreach ($values as $value) {
        $valueId = (int) $value->getOptionTypeId();
      
        $rowId = (int) $value->getRowId();							
        $valueId_by_row_id['orig'][$rowId] = $valueId;
            
        $children['orig'][$valueId] = explode(',', $value->getChildren());	
        
        $pickerImage = isset($pickerImages[$valueId]) ? $pickerImages[$valueId] : '';
      
        $image = '';
        if ($pickerImage != '') {
          $image = $pickerImage;
        } else {
          $image = $value->getImage();
        } 
      
        $largeImage = '';
        if ($value->getImage() != '') {
          $largeImage = $value->getImage();
        } else {
          $largeImage = $pickerImage; 
        }			
          
        $this->prepareImages($image, $largeImage);					
                  
        $this->config[1][$valueId][0] = $image;						
        $this->config[1][$valueId][1] = $value->getDescription() != '' ? $this->_catalogData->getPageTemplateProcessor()->filter($value->getDescription()) : '';	
        $this->config[1][$valueId][2] = array();	
        $this->config[1][$valueId][3] = array();
        $this->config[1][$valueId][4] = $largeImage;        
        				
      }	




      if ($inPreConfigured){
        foreach ($configValues as $optionId => $v){
          $this->config[0][$optionId][3] = array();			
          foreach($v as $valueId)
              $this->config[0][$optionId][3][] = (int) $valueId;						  		
        }		
      } else {		
        foreach ($sd as $templateId => $v){	
          foreach ($v as $optionId => $vv){
            $this->config[0][$optionId][3] = array();			  		
            foreach($vv as $rowId)
              if ($rowId != '')
                $this->config[0][$optionId][3][] = $valueId_by_row_id[$templateId][(int)$rowId];
          }
        }
      }


      foreach ($children as $templateId => $v){
        foreach ($v as $valueId => $vv){
          foreach ($vv as $rowId){
            if ($rowId != ''){
              if (isset($option_id_by_row_id[$templateId][(int)$rowId])){
                $this->config[1][$valueId][2][] = $option_id_by_row_id[$templateId][(int)$rowId];
              } elseif (isset($valueId_by_row_id[$templateId][(int)$rowId])){			
                $this->config[1][$valueId][3][] = $valueId_by_row_id[$templateId][(int)$rowId];
              }  	
            }					
          }
        }
      }		

      return parent::_beforeToHtml();
    }
	

	
    public function getConfig()
    { 	
		  return $this->_jsonEncoder->encode($this->config);
    }
	
	

    public function prepareImages($image, $largeImage)
    { 	
      if ($image){
        $thumbnailUrl = $this->makeThumbnail($image);			
        $pickerImageUrl = $this->makePickerImage($image);
        $hoverImageUrl = $this->makeHoverImage($largeImage);			
        if ($this->thumbnailDirUrl == ''){
          $this->thumbnailDirUrl = str_replace($image, '', $thumbnailUrl);					
          $this->pickerImageDirUrl = str_replace($image, '', $pickerImageUrl);	
          $this->hoverImageDirUrl = str_replace($largeImage, '', $hoverImageUrl);												
        }	
      }
    }

		
		
    public function makeThumbnail($image)
    { 	 
      $thumbnailUrl = $this->_imageHelper->init($this->getProduct(), 'product_page_image_small', array('type'=>'thumbnail'))
        ->keepFrame(true)
  // Uncomment the following line to set Thumbnail RGB Background Color:
  //			->backgroundColor(array(246,246,246))	

  // Set Thumbnail Size:			
        ->resize(100,100)
        ->setImageFile($image)
        ->getUrl();
      return $thumbnailUrl;
	  }		
		
		
    public function makePickerImage($image)
    { 	
			$pickerImageUrl = $this->_imageHelper->init($this->getProduct(), 'product_page_image_small', array('type'=>'thumbnail'))
				->keepFrame(false)
				->resize(30,30)
        ->setImageFile($image)
        ->getUrl();			
			return $pickerImageUrl;
		}		


    public function makeHoverImage($image)
    { 	
      $hoverImageUrl = $this->_imageHelper->init($this->getProduct(), 'product_page_image_small', array('type'=>'thumbnail'))
        ->keepFrame(true)
        ->resize(150,150)
        ->setImageFile($image)
        ->getUrl();			
      return $hoverImageUrl;
    }


    public function getThumbnailDirUrl()
    { 			
			return $this->thumbnailDirUrl;
	 	}	
	
	
    public function getPickerImageDirUrl()
    { 			
			return $this->pickerImageDirUrl;
	 	}


    public function getHoverImageDirUrl()
    { 			
      return $this->hoverImageDirUrl;
    }	 


    public function getPlaceholderUrl()
    {
			return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/image.jpg');
	 	}	
	
	
    public function getProductBaseMediaUrl()
    { 			
			return $this->_mediaConfig->getBaseMediaUrl();
	 	}



    public function getProduct()
    {
      if (!$this->hasData('product')) {
          $this->setData('product', $this->_coreRegistry->registry('product'));
      }
      return $this->getData('product');
    }
    


    public function getInPreconfigured()
    { 			
			  return $this->getProduct()->hasPreconfiguredValues() ? 'true' : 'false';
	 	}	
	 	
}