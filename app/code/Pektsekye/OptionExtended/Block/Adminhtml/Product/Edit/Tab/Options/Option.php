<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options;

use Magento\Backend\Block\Widget;
use Magento\Catalog\Model\Product;

class Option extends Widget
{
    /**
     * @var Product
     */
    protected $_productInstance;

    /**
     * @var \Magento\Framework\Object[]
     */
    protected $_values;

    /**
     * @var int
     */
    protected $_itemCount = 1;

    /**
     * @var string
     */
    protected $_template = 'product/edit/options/option.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     */
    protected $_productOptionConfig;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $_configYesNo;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Product\Options\Type
     */
    protected $_optionType;

    protected $_oxOption;
    protected $_oxValue;    
    protected $_fileSizeService;
    protected $_imageHelper;
    protected $_jsonEncoder;        
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Config\Model\Config\Source\Yesno $configYesNo
     * @param \Magento\Catalog\Model\Config\Source\Product\Options\Type $optionType
     * @param Product $product
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param array $data
     */
    public function __construct(
        \Pektsekye\OptionExtended\Model\Option $oxOption,   
        \Pektsekye\OptionExtended\Model\Value $oxValue,        
        \Magento\Framework\File\Size $fileSize,          
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $configYesNo,
        \Magento\Catalog\Model\Config\Source\Product\Options\Type $optionType,
        Product $product,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        \Magento\Catalog\Helper\Image $imageHelper, 
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,      
        array $data = array()
    ) {
  
        $this->_oxOption = $oxOption;
        $this->_oxValue = $oxValue;   
        $this->_fileSizeService = $fileSize;               
        $this->_optionType = $optionType;
        $this->_configYesNo = $configYesNo;
        $this->_product = $product;
        $this->_productOptionConfig = $productOptionConfig;
        $this->_coreRegistry = $registry;
        $this->_imageHelper = $imageHelper; 
        $this->_jsonEncoder = $jsonEncoder;                
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->_itemCount;
    }

    /**
     * @param int $itemCount
     * @return $this
     */
    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }

    /**
     * Get Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            $product = $this->_coreRegistry->registry('product');
            if ($product) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = $this->_product;
            }
        }

        return $this->_productInstance;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_productInstance = $product;
        return $this;
    }

    /**
     * Retrieve options field name prefix
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'product[options]';
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'product_option';
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getOptionsReadonly();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'duplicate_option_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Duplicate Option'), 'class' => 'add optionextended-duplicate-option-button', 'onclick' => 'optionExtended.duplicate(<%- data.id %>)')
        );    
    
        foreach ($this->_productOptionConfig->getAll() as $option) {
            $this->addChild($option['name'] . '_option_type', $option['renderer']);
        }

        return parent::_prepareLayout();
    }


    /**
     * @return mixed
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()->getBlock('admin.product.options')->getChildBlock('add_button')->getId();
        return $buttonId;
    }

    /**
     * @return mixed
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array(
                'id' => $this->getFieldId() . '_<%- data.id %>_type',
                'class' => 'select select-product-option-type required-option-select',
                'extra_params' => 'data-form-part="product_form"'
            )
        )->setName(
            $this->getFieldName() . '[<%- data.id %>][type]'
        )->setOptions(
            $this->_optionType->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * @return mixed
     */
    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array('id' => $this->getFieldId() . '_<%- data.id %>_is_require', 'class' => 'select', 'extra_params' => 'data-form-part="product_form"')
        )->setName(
            $this->getFieldName() . '[<%- data.id %>][is_require]'
        )->setOptions(
            $this->_configYesNo->toOptionArray()
        );

        return $select->getHtml();
    }


    public function getLayoutSelectHtml()
    {

      $select = $this->getLayout()->createBlock(
          'Magento\Framework\View\Element\Html\Select'
      )->setData(
          array('id' => 'ox_layout_<%- data.id %>', 'class' => 'select', 'extra_params' => 'data-form-part="product_form" onchange="optionExtended.changePopup(<%- data.id %>);"')
      )->setName(
          $this->getFieldName() . '[<%- data.id %>][layout]'
      )->setOptions(
          array(array('value' =>'above', 'label'=>__('Above Option')))
      );
              
      return $select->getHtml();
    }
   
   
    
    public function getDuplicateOptionButtonHtml()
    {
        return $this->getChildHtml('duplicate_option_button');
    }
    
        
    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $canEditPrice = $this->getCanEditPrice();
        $canReadPrice = $this->getCanReadPrice();
        $this->getChildBlock('select_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('file_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('date_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $this->getChildBlock('text_option_type')->setCanReadPrice($canReadPrice)->setCanEditPrice($canEditPrice);

        $templates = $this->getChildHtml(
            'text_option_type'
        ) . "\n" . $this->getChildHtml(
            'file_option_type'
        ) . "\n" . $this->getChildHtml(
            'select_option_type'
        ) . "\n" . $this->getChildHtml(
            'date_option_type'
        );

        return $templates;
    }


    public function getSectionTitlesJson()
    {
        $options = array();        
        foreach ((array)$this->getProduct()->getOptions() as $option) {
          if ($option->getIsTemplateOption())
            continue;
          $options[] = array(
            'id' => (int) $option->getOptionId(),
            'title' => $option->getTitle()
          );
        }

        return $this->_jsonEncoder->encode($options);
    }





    public function getIds($lastOptionId = null, $lastValueId = 0, $lastRowId = 0, $lastSortOrder = 0)
    {      
        $config = array( 
          'optionIds' => array(), 
          'optionTypes' => array(), 

          'rowIds' => array(), 
          'rowIdIsset' => array(), 
          'rowIdByOption' => array(),          
          'rowIdsByOption' => array(),
          'rowIdsByOptionIsset' => array(),      
          'rowIdBySelectId' => array(), 
 
          'optionByRowId' => array(),
          'selectIdByRowId' => array(), 
          'childrenByRowId' => array(),          
                 
          'parentRowIdsOfRowId' => array(),                  
                   
          'optionTitles' => array(),            
          'valueTitles' => array(),
           
          'lastRowId' => 0, 
          'lastOptionId' => 0,
          'lastSortOrder' => 0,
          
          'oIdsToResave' => array()                                                                                                            
        ); 

        $oxOption = array();                              
        $collection = $this->_oxOption->getCollection()
          ->addFieldToFilter('product_id', (int) $this->getProduct()->getId());           
        foreach ($collection as $item){        
           if ($item->getRowId() > 0)             	  		
            $oxOption[$item->getOptionId()] = (int) $item->getRowId() + $lastRowId;	  	                                          
        }	            

        $oxValue = array();
        $collection = $this->_oxValue->getCollection()
          ->addFieldToFilter('product_id', (int) $this->getProduct()->getId());           
        foreach ($collection as $item){     
          $oxValue[$item->getOptionTypeId()]['row_id']   = (int) $item->getRowId() + $lastRowId;                            
          $oxValue[$item->getOptionTypeId()]['children'] = $item->getChildren();
        }
                
        $nextOptionId = (int) $lastOptionId + 1;                     
        foreach ((array)$this->getProduct()->getOptions() as $option) {
            if ($option->getIsTemplateOption())
              continue;        
            $optionId = (int) $option->getOptionId();
            $oId = !is_null($lastOptionId) ? $nextOptionId : $optionId;
                        
            if (isset($oxOption[$optionId])){
              $rowId = $oxOption[$optionId];
              
              $config['rowIds'][] = $rowId;
              $config['rowIdIsset'][$rowId] = 1;			  					  		
              $config['rowIdByOption'][$oId] = $rowId;
              $config['optionByRowId'][$rowId] = $oId;                         
            } 
            
            $config['optionIds'][] = $oId;                 
            $config['optionTypes'][$oId] = $option->getType();
            $config['optionTitles'][$oId] = $option->getTitle();
            
            $sortOrder = (int) $option->getSortOrder() + $lastSortOrder;             
            if ($sortOrder > $config['lastSortOrder'])                
              $config['lastSortOrder'] = $sortOrder;
              
            $config['rowIdsByOption'][$oId] = array();
            $config['rowIdsByOptionIsset'][$oId] = array();
             
            if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {                 
                $nextValueId = $lastValueId + 1;                       
                foreach ((array)$option->getValues() as $_value) {
    
                  $optionTypeId = (int) $_value->getOptionTypeId();
                  $vId = $lastValueId > 0 ? $nextValueId : $optionTypeId;
                                      
                  if (isset($oxValue[$optionTypeId])){
                  
                    $rowId = $oxValue[$optionTypeId]['row_id'];

                    $children = array();
                    if ($oxValue[$optionTypeId]['children'] != ''){
                      $children = explode(',', $oxValue[$optionTypeId]['children']);
                      foreach ($children as $k => $v){
                        $id = (int) $v + $lastRowId;
                        $children[$k] = $id;
                        $config['parentRowIdsOfRowId'][$id][] = $rowId;
                      }  
                    }	
                                         
                    $config['selectIdByRowId'][$rowId] = $vId;						
                    $config['rowIds'][] = $rowId;
                    $config['rowIdIsset'][$rowId] = 1;			  			  	
                    $config['rowIdsByOption'][$oId][] = $rowId;
                    $config['rowIdsByOptionIsset'][$oId][$rowId] = 1;			  			
                    $config['rowIdBySelectId'][$vId] = $rowId;
                    $config['childrenByRowId'][$rowId] = $children;
                    $config['optionByRowId'][$rowId] = $oId;        	  			  	  			  												
                  }	
                  
                  $config['valueTitles'][$vId] = $_value->getTitle();

                  $nextValueId++;                                                                                    
                }
            }
            $nextOptionId++;              
        }
        
        if (isset($config['rowIds'])){
          $t = $config['rowIds'];
          sort($t);
          $config['lastRowId'] = end($t);			
        }        
               
        if (isset($config['optionIds'])){
          $t = $config['optionIds'];
          sort($t);
          $config['lastOptionId'] = end($t);			
        }        


        foreach ((array)$this->getProduct()->getOptions() as $option) {
        
            if ($option->getIsTemplateOption())
              continue;            
        
            $oId = (int) $option->getOptionId();
            
            $resaveOption = false;
            
            if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) { 
            
                foreach ((array)$option->getValues() as $_value) {                       
                  $vId = (int) $_value->getOptionTypeId();

                  if (!isset($config['rowIdBySelectId'][$vId])){
                    $rowId = $config['lastRowId'] + 1;		
         
                    $config['selectIdByRowId'][$rowId] = $vId;						
                    $config['rowIds'][] = $rowId;
                    $config['rowIdIsset'][$rowId] = 1;			  			  	
                    $config['rowIdsByOption'][$oId][] = $rowId;
                    $config['rowIdsByOptionIsset'][$oId][$rowId] = 1;			  			
                    $config['rowIdBySelectId'][$vId] = $rowId;
                    $config['childrenByRowId'][$rowId] = array();
                    $config['optionByRowId'][$rowId] = $oId; 	

                    $resaveOption = true;

                    $config['lastRowId'] += 1;                        
                  }			              				              
                }
                
                if ($resaveOption)
                  $config['oIdsToResave'][] = $oId;                                    
            
            } else {
            
              if (!isset($config['rowIdByOption'][$oId])){
                $rowId = $config['lastRowId'] + 1;
                
                $config['rowIds'][] = $rowId;
                $config['rowIdIsset'][$rowId] = 1;			  					  		
                $config['rowIdByOption'][$oId] = $rowId;
                $config['optionByRowId'][$rowId] = $oId;
                
                $config['oIdsToResave'][] = $oId;
                
                $config['lastRowId'] += 1;
              } 	                
            
            }                
            
            
        } 

        return $config;
    }



    public function getOptionData($optionId, $nextOptionId = null, $lastValueId = 0, $lastRowId = 0, $lastSortOrder = 0)
    {

        $data = array();        


        $idsConfig = $this->getIds();

        $option = $this->getProduct()->getOptionById($optionId);        
        

        $showPrice = $this->getCanReadPrice();
                   
        $scope = (int)$this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $oxOption = array();
        
        $collection = $this->_oxOption->getCollection()
          ->joinNotes((int) $this->getProduct()->getStoreId())
          ->addFieldToFilter('option_id', (int) $optionId);                      
        foreach ($collection as $item){
           $oxOption['note']   = $item->getNote();
           $oxOption['store_note'] = $item->getStoreNote();           
           $oxOption['code']   = is_null($nextOptionId) ? $item->getCode() : '';
           $oxOption['layout'] = $item->getLayout();
           $oxOption['popup']  = $item->getPopup(); 
           $oxOption['selected_by_default']  = $item->getSelectedByDefault();    
           if ($item->getRowId() > 0){             
             $oxOption['row_id']  = (int) $item->getRowId() + $lastRowId;                                				 		  	  
           }		  																                                                    
        }	


        $sdIds = array();
        if (count($oxOption) > 0){
          $data = $oxOption;
          if ($oxOption['selected_by_default'] != ''){
            $ids = explode(',', $oxOption['selected_by_default']); 
            foreach($ids as $id)
              $sdIds[] = (int) $id + $lastRowId;
          }                 				                         
        }

        if (!isset($data['row_id']) && isset($idsConfig['rowIdByOption'][$optionId])){
          $data['row_id'] = $idsConfig['rowIdByOption'][$optionId];
        }
        
        $oId = $nextOptionId ? $nextOptionId : $option->getOptionId();
              
        $data['id'] = $oId;
    //    $data['item_count'] = $oId;
        $data['option_id'] = $oId;
        $data['title'] = $option->getTitle();
        $data['type'] = $option->getType();
        $data['is_require'] = $option->getIsRequire();
        $data['sort_order'] = $option->getSortOrder() + $lastSortOrder;
        $data['can_edit_price'] = $this->getCanEditPrice();

        if ($this->getProduct()->getStoreId() != '0') {
            $data['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                $oId,
                'title',
                is_null($option->getStoreTitle())
            );
            $data['scopeTitleDisabled'] = is_null($option->getStoreTitle()) ? 'disabled' : null;
            
            $data['checkboxScopeNote'] = $this->getCheckboxScopeNoteHtml(
                $oId,
                is_null($oxOption['store_note'])
            );
            $data['scopeNoteDisabled'] = is_null($oxOption['store_note']) ? 'disabled' : null;               
        }

        if ($option->getGroupByType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {                 
                
          $oxValue = array();

          $optionTypeIds = array();
          foreach ($option->getValues() as $_value)
            $optionTypeIds[] = $_value->getOptionTypeId();
              
          $collection = $this->_oxValue->getCollection()
            ->joinDescriptions((int) $this->getProduct()->getStoreId())
            ->addFieldToFilter('option_type_id', $optionTypeIds);           
          foreach ($collection as $item){
            $optionTypeId = (int) $item->getOptionTypeId();
        
            $image = $item->getImage();
        
            $imageUrl = '';
            if (!empty($image)){
              $imageUrl = $this->_imageHelper->init($this->getProduct(), 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
            }
            
            $children = '';
            if ($item->getChildren() != ''){
              $ids = explode(',', $item->getChildren()); 
              foreach($ids as $id){
                $id = (int) $id + $lastRowId;
                $children .= ($children != '' ? ',' : '') . $id;
              }  
            }
        
            $oxValue[$optionTypeId]['row_id']         = (int) $item->getRowId() + $lastRowId;               
            $oxValue[$optionTypeId]['image']          = $imageUrl;
            $oxValue[$optionTypeId]['image_saved_as'] = $image;              
            $oxValue[$optionTypeId]['children']       = $children;
            $oxValue[$optionTypeId]['descr']          = $item->getDescription();
            $oxValue[$optionTypeId]['store_descr']    = $item->getStoreDescription();             
          }
                                 
          $i = 0;
          $itemCount = 0;
          $nextValueId = $lastValueId + 1;          
          foreach ($option->getValues() as $_value) {
              $vId = $lastValueId > 0 ? $nextValueId : (int) $_value->getOptionTypeId();

              $data['optionValues'][$i] = array(
                  'item_count' => max($itemCount, $vId),
                  'option_id' => $oId,
                  'option_type_id' => $vId,
                  'title' => $_value->getTitle(),
                  'price' => $showPrice ? $this->getPriceValue(
                      $_value->getPrice(),
                      $_value->getPriceType()
                  ) : '',
                  'price_type' => $showPrice ? $_value->getPriceType() : 0,
                  'sku' => $_value->getSku(),
                  'sort_order' => $_value->getSortOrder()                          
              );
              
              $optionTypeId = (int) $_value->getOptionTypeId();              
              if (isset($oxValue[$optionTypeId])){              
                $rowId = $oxValue[$optionTypeId]['row_id'];                                        
                $data['optionValues'][$i] = array_merge($data['optionValues'][$i], $oxValue[$optionTypeId]);                   
                $data['optionValues'][$i]['sd_checked'] = in_array($rowId, $sdIds);                             	  			  	  			  												
              }	                                                                                    

              if (!isset($data['optionValues'][$i]['row_id']) && isset($idsConfig['rowIdBySelectId'][$vId])){
                $data['optionValues'][$i]['row_id'] = $idsConfig['rowIdBySelectId'][$vId];
              }

              if ($this->getProduct()->getStoreId() != '0') {
                  $data['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml(
                      $oId,
                      'title',
                      is_null($_value->getStoreTitle()),
                      $vId
                  );
                  $data['optionValues'][$i]['scopeTitleDisabled'] = is_null(
                      $_value->getStoreTitle()
                  ) ? 'disabled' : null;
                  
                  if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
                      $data['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                          $oId,
                          'price',
                          is_null($_value->getstorePrice()),
                          $vId
                      );
                      $data['optionValues'][$i]['scopePriceDisabled'] = is_null(
                          $_value->getStorePrice()
                      ) ? 'disabled' : null;
                  }
                  
                  $data['optionValues'][$i]['checkboxScopeDescription'] = $this->getCheckboxScopeDescriptionHtml(
                      $oId,
                      $vId,
                      !isset($oxValue[$optionTypeId]['store_descr'])
                  );
                  $data['optionValues'][$i]['scopeDescriptionDisabled'] = !isset($oxValue[$optionTypeId]['store_descr']) ? 'disabled' : null;                  
              }
              $i++;
              $nextValueId++;
          }
        } else {
            $data['price'] = $showPrice ? $this->getPriceValue(
                $option->getPrice(),
                $option->getPriceType()
            ) : '';
            $data['price_type'] = $option->getPriceType();
            $data['sku'] = $option->getSku();
            $data['max_characters'] = $option->getMaxCharacters();
            $data['file_extension'] = $option->getFileExtension();
            $data['image_size_x'] = $option->getImageSizeX();
            $data['image_size_y'] = $option->getImageSizeY();
            if ($this->getProduct()->getStoreId() != '0'
                && $scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE
            ) {
                $data['checkboxScopePrice'] = $this->getCheckboxScopeHtml(
                    $oId,
                    'price',
                    is_null($option->getStorePrice())
                );
                $data['scopePriceDisabled'] = is_null($option->getStorePrice()) ? 'disabled' : null;
            }
        }

        return $data;
    }




    public function getImportFromProductData($lastOptionId, $lastValueId, $lastRowId, $lastSortOrder)
    {
        $data = array('sectionTitles' => array(),'optionData' => array());
        
        $data['ids'] = $this->getIds($lastOptionId, $lastValueId, $lastRowId, $lastSortOrder);
        
        $nextOptionId = $lastOptionId + 1;        
        foreach ((array)$this->getProduct()->getOptions() as $option) {
          if ($option->getIsTemplateOption())
            continue;
          $data['sectionTitles'][] = array(
            'id' => (int) $nextOptionId,
            'title' => $option->getTitle()
          );
          
          $data['optionData'][$nextOptionId] = $this->getOptionData($option->getOptionId(), $nextOptionId, $lastValueId, $lastRowId, $lastSortOrder);
          
          $nextOptionId++;          
        }
        
        return $data;
    }



    public function getIdsJson()
    { 
        return $this->_jsonEncoder->encode($this->getIds());    
    }
    
    


    /**
     * Retrieve html of scope checkbox
     *
     * @param string $id
     * @param string $name
     * @param boolean $checked
     * @param string $select_id
     * @return string
     */
    public function getCheckboxScopeHtml($id, $name, $checked = true, $select_id = '-1', array $containers = [])
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($select_id != '-1') {
            $selectNameHtml = '[values][' . $select_id . ']';
            $selectIdHtml = 'select_' . $select_id . '_';
        }
        $containers[] = '$(this).up(1)';
        $containers = implode(',', $containers);
        $localId = $this->getFieldId() . '_' . $id . '_' . $selectIdHtml . $name . '_use_default';
        $localName = "options_use_default[" . $id . "]" . $selectNameHtml . "[" . $name . "]";
        $useDefault =
            '<div class="field-service">'
            . '<input data-form-part="product_form" type="checkbox" class="use-default-control"'
            . ' name="' . $localName . '"' . 'id="' . $localId . '"'
            . ' value=""'
            . $checkedHtml
            . ' onchange="toggleSeveralValueElements(this, [' . $containers . ']);" '
            . ' />'
            . '<label for="' . $localId . '" class="use-default">'
            . '<span class="use-default-label">' . __('Use Default') . '</span></label></div>';

        return $useDefault;
    }
    
    
    
    
    public function getCheckboxScopeNoteHtml($optionId, $checked = true)
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }

        $localId = "ox_note_{$optionId}_use_default";
        $localName = "{$this->getFieldName()}[{$optionId}][scope][optionextended_note]";        
        $useDefault =
            '<div class="field-service">'
            . '<input data-form-part="product_form" type="checkbox" class="use-default-control"'
            . ' name="' . $localName . '"' . 'id="' . $localId . '"'
            . ' value="1"'
            . $checkedHtml
            . ' onchange="toggleSeveralValueElements(this, [$(this).up(1)]);" '
            . ' />'
            . '<label for="' . $localId . '" class="use-default">'
            . '<span class="use-default-label">' . __('Use Default') . '</span></label></div>';

        return $useDefault;
    } 
    
    
    
    
    public function getCheckboxScopeDescriptionHtml($optionId, $valueId, $checked = true)
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }

        $localId = "ox_description_{$valueId}_use_default";
        $localName = "{$this->getFieldName()}[{$optionId}][values][{$valueId}][scope][optionextended_description]";        
        $useDefault =
            '<div class="field-service">'
            . '<input data-form-part="product_form" type="checkbox" class="use-default-control"'
            . ' name="' . $localName . '"' . 'id="' . $localId . '"'
            . ' value="1"'
            . $checkedHtml
            . ' onchange="toggleSeveralValueElements(this, [$(this).up(1)]);" '
            . ' />'
            . '<label for="' . $localId . '" class="use-default">'
            . '<span class="use-default-label">' . __('Use Default') . '</span></label></div>';

        return $useDefault;
    }        

    /**
     * @param float $value
     * @param string $type
     * @return string
     */
    public function getPriceValue($value, $type)
    {
        if ($type == 'percent') {
            return number_format($value, 2, null, '');
        } elseif ($type == 'fixed') {
            return number_format($value, 2, null, '');
        }
    }

    /**
     * Return product grid url for custom options import popup
     *
     * @return string
     */
    public function getProductGridUrl()
    {
        return $this->getUrl('catalog/*/optionsImportGrid');
    }

    /**
     * Return custom options getter URL for ajax queries
     *
     * @return string
     */
    public function getCustomOptionsUrl()
    {
        return $this->getUrl('catalog/*/customOptions');
    }
    

    /**
     * Get file size
     *
     * @return \Magento\Framework\File\Size
     */
    public function getFileSizeService()
    {
        return $this->_fileSizeService;
    }
    
    
    
    public function getTemplateDataUrl()
    {
        return $this->getUrl('optionextended/optiontemplate/templateData');        
    }    
    
  
}
