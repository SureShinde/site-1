<?php

namespace Pektsekye\OptionExtended\Model;

class Template extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{   
 
    protected $_product;   
    protected $_productOption;     
    protected $_imageHelper;       
    
    public function __construct(   
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Product\Option $productOption,                     
        \Magento\Catalog\Helper\Image $imageHelper,     
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry, 
        \Pektsekye\OptionExtended\Model\ResourceModel\Template $resource,                               
        \Pektsekye\OptionExtended\Model\ResourceModel\Template\Collection $resourceCollection,                                    
        array $data = array()
    ) {                  
        $this->_product = $product;    
        $this->_productOption = $productOption;          
        $this->_imageHelper = $imageHelper;                 
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    public function addOptionTemplates($product)
    {
      $options = $this->getResource()->getTemplateOptionsCollection($product->getId(),(int) $product->getStoreId());

      $coreOptions = $this->_productOption->getCollection()->addFieldToFilter('product_id', $product->getId());
      
      $coreOIds = []; 
      foreach($coreOptions as $option){
        $coreOIds[$option->getId()] = 1;
      }
      
      foreach ((array) $product->getOptions() as $option) {
        if (isset($coreOIds[$option->getId()]))// don't add duplicates
          $options[] = $option;
      }
      
      usort($options, array($this, "sortOptions"));

      $product->setData('options', []);
       
      $hasRequired = false;
      foreach ($options as $option){

        $option->setProduct($product); 
        $product->addOption($option);
        if (!$hasRequired && $option->getIsRequire() == 1)
          $hasRequired = true;
      }
    
      if (count($options) > 0){
        $product->setHasOptions(true);
        $product->setRequiredOptions($hasRequired);
        $product->setOptionTemplatesAdded(true);      
      }

      return $this;
    }  
  
  
    public function sortOptions($o1, $o2)
    {
      $a = (int) $o1->getSortOrder();
      $b = (int) $o2->getSortOrder();	
      if ($a == $b)
          return 0;
      return ($a < $b) ? -1 : 1;
    }


    public function getOptionsData($templateId)
    {               

		  $typeIndexes = array(						
			  'field'		  => 1,	
			  'area' 		  => 2,	
			  'file' 		  => 3,	
			  'drop_down' => 4,	
			  'radio' 		=> 5,	
			  'checkbox'  => 6,	
			  'multiple'  => 7,				
			  'date' 		  => 8,					
			  'date_time' => 9,	
			  'time'			=> 10											
		  );

      $layoutIndexes = array(
        'radio' => array(
            'above'       =>0,        
            'before'      =>1,
            'below'       =>2,
            'swap'        =>3,
            'grid'        =>4,
            'gridcompact' =>5,                 
            'list'        =>6               
          ),        
        'checkbox' => array(
            'above'       =>0,         
            'below'       =>1,
            'grid'        =>2, 
            'gridcompact' =>3,               
            'list'        =>4    
          ),        
        'drop_down' => array(
            'above'     =>0,         
            'before'    =>1,
            'below'     =>2,
            'swap'      =>3,
            'picker'    =>4, 
            'pickerswap'=>5                 
          ),
        'multiple' => array(
            'above'=>0,        
            'below'=>1         
          )           
      );

      $oi = 0;            
      $js = array();
      $options = $this->getResource()->getTemplateData($templateId, 0);      
			foreach ($options as $k => $option){
		
        $js[$oi] = array(       
          'title'         => (string) $option->getTitle(),
          'type'          => (string) $option->getType(),       
          'typeIndex'     => (int) $typeIndexes[$option->getType()],                   
          'isRequired'    => $option->getIsRequire() == 1,         
          'sortOrder'     => (int) $option->getSortOrder(),
          'price'         => number_format($option->getPrice(), 2, null, ''),
          'priceType'     => (string) $option->getPriceType(),
          'priceTypeIndex'=> $option->getPriceType() == 'percent' ? 1 : 0,          
          'sku'           => (string) $option->getSku(),
          'maxCharacters' => (string) $option->getMaxCharacters(),
          'fileExtension' => (string) $option->getFileExtension(),
          'imageSizeX'    => (int) $option->getImageSizeX(),
          'imageSizeY'    => (int) $option->getImageSizeY(),
          'note'          => (string) $option->getNote(),     
          'layoutIndex'   => isset($layoutIndexes[$option->getType()]) ? (int) $layoutIndexes[$option->getType()][$option->getLayout()] : 0,         
          'popupChecked'  => $option->getPopup() == 1,  
          'popupDisabled' => $option->getLayout() == 'swap'                                                                          
        );

        if (!is_null($option->getRowId()))
          $js[$oi]['rowId'] = (int) $option->getRowId();  
		
        if (!is_null($option->getValues())){
        
          $vi = 0;        
			    foreach ($option->getValues() as $kk => $value) {
			    
            $imageUrl = '';
		        if ($value->getImage() != ''){
		          $imageUrl = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($value->getImage())->getUrl();
            }
                       
            $sdChecked = false;
            if ($option->getSelectedByDefault() != '')
              $sdChecked = in_array($value->getRowId(), explode(',', $option->getSelectedByDefault()));

            $children = array();
		        if ($value->getChildren() != ''){            
              $children = explode(',', $value->getChildren());
              foreach ($children as $k => $v)
                $children[$k] = (int) $v;               
            }
            
            $js[$oi]['values'][$vi] = array(
              'rowId'           => (int) $value->getRowId(),            		           
              'title'           => (string) $value->getTitle(),			
              'price'           => number_format($value->getPrice(), 2, null, ''),									
              'priceTypeIndex'  => $value->getPriceType() == 'percent' ? 1 : 0, 
              'sku'             => (string) $value->getSku(),
              'sortOrder'       => (string) $value->getSortOrder(),			         
              'imageUrl'        => $imageUrl,          
              'imageSavedAs'    => $value->getImage(),                       			  																		
              'description'     => (string) $value->getDescription(),
              'sdIsChecked'     => $sdChecked,
              'children'        => $children      			  		
            );
            $vi++;
			    }
			    
        }
        $oi++; 
		  }
		  
      return $js;
    }


    public function getTemplatesCsv()
    {
      return $this->getResource()->getTemplatesCsv();    
    }    
    

    public function getTemplateProductsCsv()
    {
      return $this->getResource()->getTemplateProductsCsv();  
    }      
    
    
    public function getIdentities()
    {
   
      $identities = array();
      
      $productIds = $this->getProductIds();
      
      if (empty($productIds))
        $productIds = (array) $this->getResource()->getProductIds($this->getId());
      
      if ($this->getOldProductIds())
        $productIds = array_unique(array_merge($productIds, (array) $this->getOldProductIds()));

      foreach ($productIds as $productId) {
        $identities[] = \Magento\Catalog\Model\Product::CACHE_TAG . '_' . $productId;
      }

      return $identities;
    }    
    
}