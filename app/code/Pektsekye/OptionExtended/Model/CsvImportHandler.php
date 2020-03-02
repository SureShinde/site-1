<?php

namespace Pektsekye\OptionExtended\Model;

class CsvImportHandler
{

    protected $_oldSku = array();

    protected $_selector;
    protected $_selectorLevel;
    protected $_dbModel;
    protected $_productFactory;
    protected $_option;
    protected $_resource;
    protected $_mediaConfig;
    protected $_mediaDirectory;   
    protected $_storeManager;               
    protected $_csvParser;     
    
    public function __construct(
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Catalog\Model\ProductFactory $productFactory,  
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\File\Csv $csvParser                                                
    ) {
        $this->_option = $option;
        $this->_productFactory = $productFactory; 
        $this->_resource = $resource;        
        $this->_mediaConfig = $mediaConfig; 
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);  
        $this->_storeManager = $storeManager;
        $this->_csvParser = $csvParser;                              
          
        $this->_initSkus();                       
    }

    /**
     * Initialize existent product SKUs.
     *
     * @return $this
     */
    protected function _initSkus()
    {
        $columns = array('entity_id', 'sku');
        foreach ($this->_productFactory->create()->getProductEntitiesInfo($columns) as $info) {
          $this->_oldSku[$info['sku']] = $info['entity_id'];
        }
        return $this;
    }
 
 
 
    public function importFromCsvFile($file, $importType)
    { 
    
      if (!isset($file['tmp_name'])) {
          throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
      }
      
      $rawData = $this->_csvParser->getData($file['tmp_name']);

      $fieldNames = array();                
      foreach ($rawData[0] as $v) {
        $v = strtolower( preg_replace('/\s+/', '_', preg_replace('/[^\w\s]/', '', $v)));
        if ($v == '' || in_array($v, $fieldNames)){
          throw new \Magento\Framework\Exception\LocalizedException(__('Import failed. The first row in the import.csv file must contain unique column names.'));
          return;          
        } 
        $fieldNames[] = $v;
      }
                  
      switch($importType){
        case 'options':
          $this->importOptions($rawData, $fieldNames);
          break;
        case 'values':
          $this->importValues($rawData, $fieldNames);
          break; 
        case 'options_translate':
          $this->importOptionsTranslate($rawData, $fieldNames);
          break;
        case 'values_translate':
          $this->importValuesTranslate($rawData, $fieldNames);
          break; 
        case 'templates':
          $this->importTemplates($rawData, $fieldNames);
          break;    
        case 'template_products':
          $this->importTemplateProducts($rawData, $fieldNames);
          break;                              
        case 'template_options':
          $this->importTemplateOptions($rawData, $fieldNames);
          break;
        case 'template_values':
          $this->importTemplateValues($rawData, $fieldNames);
          break; 
        case 'template_options_translate':
          $this->importTemplateOptionsTranslate($rawData, $fieldNames);
          break;
        case 'template_values_translate':
          $this->importTemplateValuesTranslate($rawData, $fieldNames);
          break;                                                         
      } 
    
    }
    
    
    
    public function importOptions($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
        
        
        $types = array(
        'date' => 1,
        'date_time' => 1,
        'time' => 1,
        'file' => 1,
        'field' => 1,
        'area' => 1,
        'drop_down' => 1,
        'radio' => 1,
        'checkbox' => 1,
        'multiple' => 1
	      );
	      
        $selectTypes = array(
        'drop_down' => 1,
        'radio' => 1,
        'checkbox' => 1,
        'multiple' => 1				
	      );  
	      
        $layouts = array(
          'radio' => array(
              'above'       =>1,        
              'before'      =>1,
              'below'       =>1,
              'swap'        =>1,
              'grid'        =>1,  
              'gridcompact' =>1,                 
              'list'        =>1               
            ),        
          'checkbox' => array(
              'above'       =>1,         
              'below'       =>1,
              'grid'        =>1, 
              'gridcompact' =>1,                 
              'list'        =>1    
            ),        
          'drop_down' => array(
              'above'     =>1,         
              'before'    =>1,
              'below'     =>1,
              'swap'      =>1,
              'picker'    =>1, 
              'pickerswap'=>1                 
            ),
          'multiple' => array(
              'above'=>1,        
              'below'=>1         
            )           
        );               

        $productIds = $connection->fetchAssoc("SELECT `sku`,`entity_id` FROM {$connection->getTableName('catalog_product_entity')}");

        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('catalog_product_option')}'");
        $nextOptionId = $r['Auto_increment'];
        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('optionextended_option')}'");
        $nextOxOptionId = $r['Auto_increment'];

        $toProductOptionTable = "INSERT INTO `{$connection->getTableName('catalog_product_option')}` (`option_id`,`product_id`,`type`,`is_require`,`sku`,`max_characters`,`file_extension`,`image_size_x`,`image_size_y`,`sort_order`) VALUES ";
        $toProductOptionTitleTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_title')}` (`option_id`,`title`) VALUES ";      
        $toProductOptionPriceTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_price')}` (`option_id`,`price`,`price_type`) VALUES ";
        $toOptionextendedOptionTable = "INSERT INTO `{$connection->getTableName('optionextended_option')}` (`ox_option_id`,`option_id`,`product_id`,`code`,`row_id`,`layout`,`popup`,`selected_by_default`) VALUES ";
        $toOptionextendedOptionNoteTable = "INSERT INTO `{$connection->getTableName('optionextended_option_note')}` (`ox_option_id`,`note`) VALUES ";

        $importedCodes = array();
        $pIds = array();
        $rpIds = array();
        $rowIds = array(); 
        $toPOT=$toPOTT=$toPOPT=$toOOT=$toOONT='';

        $countRows = 0;    
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			    if (empty($d['product_sku'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'product_sku'));
            return;                       
          }
          
			    if (!isset($productIds[$d['product_sku']])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Product with specified SKU "%1" is not found', $d['product_sku']));
            return;                       
          }   
                 
          $productId = $productIds[$d['product_sku']]['entity_id'];                
  
          if (empty($d['code'])){            
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'code'));
            return;    
          }
             
          if (isset($importedCodes[$d['code']])) {           
            throw new \Magento\Framework\Exception\LocalizedException(__('Option with %1 "%2" has been already imported', 'code', $d['code']));
            return;    
          }    
                   
          $importedCodes[$d['code']] = 1;
              
			    if (empty($d['title'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'title'));
            return;                       
          }          
          
			    if (empty($d['type'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'type'));
            return;                       
          }             
          
          if (!isset($types[$d['type']])){
            throw new \Magento\Framework\Exception\LocalizedException(__('Value "%1" is not valid for field "%2". Valid values for the field "%3" are: %4.', $d['type'], 'type', 'type', implode(", ", array_keys($types))));
            return;                             
          }
         
          if (!isset($selectTypes[$d['type']])){
            if (empty($d['row_id'])){        
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" of the option type "%2" is not defined', 'row_id', $d['type']));
              return;   
            }
            if (isset($rowIds[$productId][$d['row_id']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with %1 "%2" for product #%3 has been already imported', 'row_id', $d['row_id'], $productId));
              return;       
            }
            $rowIds[$productId][$d['row_id']] = 1;                      
          }          
        
                                   
          $type = $connection->quote($d['type']); 
          $isRequire = (int) $d['is_require'];  
          $sku = $connection->quote($d['sku']);        
          $maxCharacters   = !empty($d['max_characters']) ? (int) $d['max_characters'] : 'NULL';
          $fileExtension   = !empty($d['file_extension']) ? $connection->quote($d['file_extension']) : 'NULL';	          
          $imageSizeX      = (int) $d['image_size_x'];
          $imageSizeY      = (int) $d['image_size_y'];
          $sortOrder = (int) $d['sort_order']; 
          $title = $connection->quote($d['title']);		  
          $price = $connection->quote($d['price']);
          $priceType = $connection->quote($d['price_type']);
          $code = $connection->quote($d['code']);		   		        
          $rowId = !empty($d['row_id']) ? (int) $d['row_id'] : 'NULL';
          $layout = isset($layouts[$d['type']][$d['layout']]) ? $connection->quote($d['layout']) : "'above'";
          $popup = $connection->quote($d['popup']);
          $selectedByDeafault = $connection->quote($d['selected_by_default']);     
          $note = $connection->quote($d['note']);

          $toPOT  .= ($toPOT != '' ? ',' : '') . "({$nextOptionId},{$productId},{$type},{$isRequire},{$sku},{$maxCharacters},{$fileExtension},{$imageSizeX},{$imageSizeY},{$sortOrder})";
          $toPOTT .= ($toPOTT != '' ? ',' : '') . "({$nextOptionId},{$title})";	       
          if (!isset($selectTypes[$d['type']]))              
            $toPOPT .= ($toPOPT != '' ? ',' : '') . "({$nextOptionId},{$price},{$priceType})";       
          $toOOT  .= ($toOOT != '' ? ',' : '') . "({$nextOxOptionId},{$nextOptionId},{$productId},{$code},{$rowId},{$layout},{$popup},{$selectedByDeafault})";
          $toOONT .= ($toOONT != '' ? ',' : '') . "({$nextOxOptionId},{$note})"; 
                         

          $pIds[$productId] = 1;
          if ($isRequire == 1)
            $rpIds[$productId] = 1;            
          $nextOptionId++;
          $nextOxOptionId++;
          
          $countRows++;
        }           


        if ($countRows > 0){ 
        
          $pIdsString = implode(',', array_keys($pIds));    
          $connection->query("DELETE FROM `{$connection->getTableName('catalog_product_option')}` WHERE `product_id` IN ({$pIdsString})");		    	
        	  	        	  	  	
          $codes = $connection->fetchCol("SELECT `code` FROM {$connection->getTableName('optionextended_option')}");																
          $duplicateCodes = array_intersect(array_keys($importedCodes), $codes);
          
          if (count($duplicateCodes) > 0){       
            throw new \Magento\Framework\Exception\LocalizedException(__('Option code(s) "%1" already exist. Stop import process.', implode(", ", $duplicateCodes)));
            return;  
          } else {
          
            $connection->query($toProductOptionTable . $toPOT);
            $connection->query($toProductOptionTitleTable . $toPOTT);						
            if ($toPOPT != '')			
              $connection->query($toProductOptionPriceTable . $toPOPT);			  
            $connection->query($toOptionextendedOptionTable . $toOOT);
            $connection->query($toOptionextendedOptionNoteTable . $toOONT); 			    
            
            $connection->query("UPDATE `{$connection->getTableName('catalog_product_entity')}` SET `has_options`=1 WHERE `entity_id` IN ({$pIdsString})");	
            if (count($rpIds) > 0)
              $connection->query("UPDATE `{$connection->getTableName('catalog_product_entity')}` SET `required_options`=1 WHERE `entity_id` IN (" . implode(',', array_keys($rpIds)) .")");	      		
              
					}
					
        }
                       
    }
    
    
    
    
    
    
    public function importValues($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
        
        $optionRows = $connection->fetchAssoc("SELECT code, option_id, product_id FROM {$connection->getTableName('optionextended_option')}");
				

        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('catalog_product_option_type_value')}'");
        $nextOptionTypeId = $r['Auto_increment'];
        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('optionextended_value')}'");
        $nextOxValueId = $r['Auto_increment'];


        $toProductOptionTypeValueTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_type_value')}` (`option_type_id`,`option_id`,`sku`,`sort_order`) VALUES ";
        $toProductOptionTypeTitleTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_type_title')}` (`option_type_id`,`title`) VALUES ";
        $toProductOptionTypePriceTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_type_price')}` (`option_type_id`,`price`,`price_type`) VALUES ";
        $toOptionextendedValueTable = "INSERT INTO `{$connection->getTableName('optionextended_value')}` (`ox_value_id`,`option_type_id`,`product_id`,`row_id`,`children`,`image`) VALUES ";
        $toOptionextendedValueDescriptionTable = "INSERT INTO `{$connection->getTableName('optionextended_value_description')}` (`ox_value_id`,`description`) VALUES ";

        $oIds   = array();
        $images = array();
        $rowIds = array(); 
        $toPOTVT=$toPOTTT=$toPOTPT=$toOVT=$toOVDT='';
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionRows[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionRows[$d['option_code']]['option_id'];             
            $productId = $optionRows[$d['option_code']]['product_id'];
            
			      if (empty($d['row_id'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'row_id'));
              return;       
            }
            
			      if (isset($rowIds[$productId][$d['row_id']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with %1 "%2" for product #%3 has been already imported', 'row_id', $d['row_id'], $productId));
              return;              
            }            
            $rowIds[$productId][$d['row_id']] = 1;

			      if (!isset($d['title']) || $d['title'] == ''){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'title'));
              return;            
            }
            
			      if (!empty($d['image']) && !isset($images[$d['image']])){
				      $image = substr($d['image'], 0, 1) != '/' ? '/' . $d['image'] : $d['image'];
				      $mediaPath = $this->_mediaDirectory->getAbsolutePath("catalog/product" . $image);	
				      if (!file_exists($mediaPath)) {																		
                throw new \Magento\Framework\Exception\LocalizedException(__('Image "%1" does not exist in the pub/media/catalog/product directory.'.$mediaPath, $d['image']));
                return;    		
				      }
              $images[$d['image']] = $image;				      
			      }                    
                    
                    

            $sku = $connection->quote($d['sku']);
            $sortOrder = (int) $d['sort_order']; 
            $title = $connection->quote($d['title']);		  
            $price = $connection->quote($d['price']);
            $priceType = $connection->quote($d['price_type']);
            $rowId = (int) $d['row_id'];		     
            $children = $connection->quote($d['children']);
            $image = isset($images[$d['image']]) ? $connection->quote($images[$d['image']]) : "''";
            $description = $connection->quote($d['description']);

            $toPOTVT .= ($toPOTVT != '' ? ',' : '') . "({$nextOptionTypeId},{$optionId},{$sku},{$sortOrder})";
            $toPOTTT .= ($toPOTTT != '' ? ',' : '') . "({$nextOptionTypeId},{$title})"; 
            $toPOTPT .= ($toPOTPT != '' ? ',' : '') . "({$nextOptionTypeId},{$price},{$priceType})";  
            $toOVT   .= ($toOVT != '' ? ',' : '') . "({$nextOxValueId},{$nextOptionTypeId},{$productId},{$rowId},{$children},{$image})";
            $toOVDT  .= ($toOVDT != '' ? ',' : '') . "({$nextOxValueId},{$description})";

            $oIds[$optionId] = 1;
            $nextOptionTypeId++;
            $nextOxValueId++;
            
            $countRows++;
        }       

        if ($countRows > 0){     
          $connection->query("DELETE FROM `{$connection->getTableName('catalog_product_option_type_value')}` WHERE `option_id` IN (" . implode(',', array_keys($oIds)) .")");		    	

          $connection->query($toProductOptionTypeValueTable . $toPOTVT);
          $connection->query($toProductOptionTypeTitleTable . $toPOTTT);
          $connection->query($toProductOptionTypePriceTable . $toPOTPT);
          $connection->query($toOptionextendedValueTable . $toOVT);
          $connection->query($toOptionextendedValueDescriptionTable . $toOVDT);    	  	      
        }        
    }            






    
    
    public function importOptionsTranslate($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();     

        $stores = $this->_storeManager->getStores(true, true);


        $optionRows = $connection->fetchAssoc("SELECT code, option_id, ox_option_id FROM {$connection->getTableName('optionextended_option')}");				

        $toProductOptionTitleTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_title')}` (`option_id`,`store_id`,`title`) VALUES ";      
        $toOptionextendedOptionNoteTable = "INSERT INTO `{$connection->getTableName('optionextended_option_note')}` (`ox_option_id`,`store_id`,`note`) VALUES ";

        $oIds   = array();
        $oxoIds = array();
        $storeIds = array();        
        $toPOTT=$toOONT=''; 
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionRows[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionRows[$d['option_code']]['option_id'];             
            $oxOptionId = $optionRows[$d['option_code']]['ox_option_id'];
            
			      if (empty($d['store'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'store'));
              return;       
            }
            
			      if (!isset($stores[$d['store']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Store with code "%1" does not exist', $d['store']));
              return;              
            }           
            
            $storeId = $stores[$d['store']]->getId();            
             
			      if (isset($storeIds[$optionId][$storeId])){  
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" and store "%2" has been already imported', $d['option_code'], $d['store']));
              return;     
            }            
            
            $storeIds[$optionId][$storeId] = 1;

            if ($storeId == 0)
              continue;  

            if ($d['title'] != '')
              $toPOTT .= ($toPOTT != '' ? ',' : '') . "({$optionId},{$storeId},{$connection->quote($d['title'])})";
            if ($d['note'] != '')            	                         
              $toOONT .= ($toOONT != '' ? ',' : '') . "({$oxOptionId},{$storeId},{$connection->quote($d['note'])})"; 

            $oIds[$optionId]     = 1;
            $oxoIds[$oxOptionId] = 1;
            
            $countRows++;
        }       

        if ($countRows > 0){
          $connection->query("DELETE FROM `{$connection->getTableName('catalog_product_option_title')}` WHERE `option_id` IN (" . implode(',', array_keys($oIds)) .") AND `store_id` != 0 ");		    	
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_option_note')}` WHERE `ox_option_id` IN (" . implode(',', array_keys($oxoIds)) .") AND `store_id` != 0 ");	 	  	

          if ($toPOTT != ''){
			      $connection->query($toProductOptionTitleTable . $toPOTT);
			    }  	
          if ($toOONT != '')			    										  
			      $connection->query($toOptionextendedOptionNoteTable . $toOONT); 			    	      							       	  	      
        }
        
     
    }



    
    
    public function importValuesTranslate($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();   

        $stores = $this->_storeManager->getStores(true, true);


        $optionRows = $connection->fetchAssoc("SELECT code, option_id FROM {$connection->getTableName('optionextended_option')}");				
        $optionValueRows = array();
        $rs = $connection->fetchAll("SELECT potv.option_id, oxv.row_id, ox_value_id, oxv.option_type_id FROM {$connection->getTableName('optionextended_value')} oxv, `{$connection->getTableName('catalog_product_option_type_value')}` potv WHERE oxv.option_type_id = potv.option_type_id");	
        foreach ($rs as $r) 
          $optionValueRows[$r['option_id']][$r['row_id']] = array('ox_value_id'=>$r['ox_value_id'],'option_type_id'=>$r['option_type_id']);

                    			
        $toProductOptionTypeTitleTable = "INSERT INTO `{$connection->getTableName('catalog_product_option_type_title')}` (`option_type_id`,`store_id`,`title`) VALUES ";
        $toOptionextendedValueDescriptionTable = "INSERT INTO `{$connection->getTableName('optionextended_value_description')}` (`ox_value_id`,`store_id`,`description`) VALUES ";
        
        
        $otIds  = array();
        $oxvIds = array();
        $storeIds = array();                
        $toPOTTT=$toOVDT='';

        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionRows[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionRows[$d['option_code']]['option_id'];             


			      if (empty($d['row_id'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'row_id'));
              return;       
            }
            
		        $rowId = (int) $d['row_id'];            

			      if (!isset($optionValueRows[$optionId][$rowId])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with row ID "%1" does not exist', $d['row_id']));
              return;      
            }

            $optionTypeId = $optionValueRows[$optionId][$rowId]['option_type_id'];             
            $oxValueId = $optionValueRows[$optionId][$rowId]['ox_value_id'];    
            
			      if (empty($d['store'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'store'));
              return;       
            }
            
			      if (!isset($stores[$d['store']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Store with code "%1" does not exist', $d['store']));
              return;              
            }           
            
            $storeId = $stores[$d['store']]->getId();            
             
			      if (isset($storeIds[$optionTypeId][$storeId])){     
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with row_id "%1" and store "%2" for option code "%3" has been already imported', $d['row_id'], $d['store'], $d['option_code']));                  
              return;     
            }            
            
            $storeIds[$optionTypeId][$storeId] = 1;

            if ($storeId == 0)
              continue;  

            if ($d['title'] != '')
              $toPOTTT .= ($toPOTTT != '' ? ',' : '') . "({$optionTypeId},{$storeId},{$connection->quote($d['title'])})";
            if ($d['description'] != '')              	                         
              $toOVDT  .= ($toOVDT != '' ? ',' : '') . "({$oxValueId},{$storeId},{$connection->quote($d['description'])})"; 

            $otIds[$optionTypeId] = 1;
            $oxvIds[$oxValueId]   = 1; 
            
            $countRows++;
        }       

        if ($countRows > 0){
          $connection->query("DELETE FROM `{$connection->getTableName('catalog_product_option_type_title')}` WHERE `option_type_id` IN (" . implode(',', array_keys($otIds)) .") AND `store_id` != 0 ");		    	
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_value_description')}` WHERE `ox_value_id` IN (" . implode(',', array_keys($oxvIds)) .") AND `store_id` != 0 ");	 	  	
          
          if ($toPOTTT != '')
	          $connection->query($toProductOptionTypeTitleTable . $toPOTTT);
          if ($toOVDT != '')			    											  
	          $connection->query($toOptionextendedValueDescriptionTable . $toOVDT); 	    	      							       	  	      
        }
        
     
    }


    
    public function importTemplates($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
        
        $toOptionextendedTemplateTable = "INSERT INTO `{$connection->getTableName('optionextended_template')}` (`title`,`code`,`is_active`) VALUES ";

        $toOT='';
        $tCodes = '';
        $codes = array(); 
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
  
            if (empty($d['code'])){ 
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'code'));    
              return;                
            }
            
			      if (isset($codes[$d['code']])){      
              throw new \Magento\Framework\Exception\LocalizedException(__('Template with %1 "%2" has been already imported', 'code', $d['code']));    
              return;        
            } 
            
            $codes[$d['code']] = 1; 

            if (empty($d['title'])){ 
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'title'));    
              return;        
            }

            $title = $connection->quote($d['title']);
            $code = $connection->quote($d['code']);            
            $isActive = (int) $d['is_active']; 
                        	               
	          $toOT  .= ($toOT != '' ? ',' : '') . "({$title},{$code},{$isActive})";
            $tCodes.= ($tCodes != '' ? ',' : '') . $code;	  
            
            $countRows++;
        }       

        if ($countRows > 0){
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template')}` WHERE `code` IN ({$tCodes})");		    	        	  	        	  	  	
			    $connection->query($toOptionextendedTemplateTable . $toOT);		    	      							       	  	      
        }
        
     
    }
    
    
 
    public function importTemplateProducts($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
        
        $productIds = $connection->fetchPairs("SELECT `sku`,`entity_id` FROM {$connection->getTableName('catalog_product_entity')}");        
        $templateIds = $connection->fetchPairs("SELECT `code`,`template_id` FROM {$connection->getTableName('optionextended_template')}");
        
        $toOptionextendedProductTemplateTable = "INSERT INTO `{$connection->getTableName('optionextended_product_template')}` (`product_id`,`template_id`) VALUES ";

        $toOPT='';
        $pIds = array();
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
  
            if (empty($d['product_sku'])){ 
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'product_sku'));    
              return;                
            } 

			      if (!isset($productIds[$d['product_sku']])){      
              throw new \Magento\Framework\Exception\LocalizedException(__('Product with SKU "%1" does not exist', $d['product_sku']));    
              return;         
            } 
            
            $productId = $productIds[$d['product_sku']];                       
            
            if (empty($d['template_code'])){ 
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'template_code'));    
              return;                
            }             
            
			      if (!isset($templateIds[$d['template_code']])){            
              throw new \Magento\Framework\Exception\LocalizedException(__('Template with code "%1" does not exist', $d['template_code']));    
              return;        
            } 
            
            $templateId = $templateIds[$d['template_code']];   
               
	          $toOPT  .= ($toOPT != '' ? ',' : '') . "({$productId},{$templateId})";
            $pIds[$productId] = 1;	
            
            $countRows++;
        }       

        if ($countRows > 0){
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_product_template')}` WHERE `product_id` IN (". implode(',', array_keys($pIds)) .")");		    	        	  	        	  	  	
			    $connection->query($toOptionextendedProductTemplateTable . $toOPT);			    	      							       	  	      
        }
        
     
    }
    
    
    
    
   
    
    public function importTemplateOptions($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
            
        $types = array(
        'date' => 1,
        'date_time' => 1,
        'time' => 1,
        'file' => 1,
        'field' => 1,
        'area' => 1,
        'drop_down' => 1,
        'radio' => 1,
        'checkbox' => 1,
        'multiple' => 1
	      );
	      
        $selectTypes = array(
        'drop_down' => 1,
        'radio' => 1,
        'checkbox' => 1,
        'multiple' => 1				
	      );  
	      
        $layouts = array(
          'radio' => array(
              'above'       =>1,        
              'before'      =>1,
              'below'       =>1,
              'swap'        =>1,
              'grid'        =>1,
              'gridcompact' =>1,                  
              'list'        =>1               
            ),        
          'checkbox' => array(
              'above'       =>1,         
              'below'       =>1,
              'grid'        =>1,
              'gridcompact' =>1,                  
              'list'        =>1    
            ),        
          'drop_down' => array(
              'above'     =>1,         
              'before'    =>1,
              'below'     =>1,
              'swap'      =>1,
              'picker'    =>1, 
              'pickerswap'=>1                 
            ),
          'multiple' => array(
              'above'=>1,        
              'below'=>1         
            )           
        );               

        $templateIds = $connection->fetchPairs("SELECT `code`,`template_id` FROM {$connection->getTableName('optionextended_template')}");        	

        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('optionextended_template_option')}'");
        $nextOptionId = $r['Auto_increment'];
				        
        $toOptionextendedTemplateOptionTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option')}` (`option_id`,`template_id`,`code`,`row_id`,`type`,`is_require`,`sku`,`max_characters`,`file_extension`,`image_size_x`,`image_size_y`,`sort_order`,`layout`,`popup`,`selected_by_default`) VALUES ";              
        $toOptionextendedTemplateOptionTitleTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option_title')}` (`option_id`,`title`) VALUES ";      
        $toOptionextendedTemplateOptionPriceTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option_price')}` (`option_id`,`price`,`price_type`) VALUES ";        
        $toOptionextendedTemplateOptionNoteTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option_note')}` (`option_id`,`note`) VALUES ";

        $importedCodes = array();
        $tIds = array();
        $rowIds = array(); 
        
        $toOTOT=$toOTOTT=$toOTOPT=$toOTONT='';   

        $countRows = 0;    
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			    if (empty($d['template_code'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'template_code'));
            return;                       
          }
          
			    if (!isset($templateIds[$d['template_code']])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Template with code "%1" is not found', $d['template_code']));
            return;                       
          }   
                 
          $templateId = $templateIds[$d['template_code']];                 
  
          if (empty($d['code'])){            
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" not defined', 'code'));
            return;    
          }
             
          if (isset($importedCodes[$d['code']])) {           
            throw new \Magento\Framework\Exception\LocalizedException(__('Option with %1 "%2" has been already imported', 'code', $d['code']));
            return;    
          }    
                   
          $importedCodes[$d['code']] = 1;
              
			    if (empty($d['title'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'title'));
            return;                       
          }          
          
			    if (empty($d['type'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'type'));
            return;                       
          }             
          
          if (!isset($types[$d['type']])){
            throw new \Magento\Framework\Exception\LocalizedException(__('Value "%1" is not valid for field "%2". Valid values for the field "%3" are: %4.', $d['type'], 'type', 'type', implode(", ", array_keys($types))));
            return;                             
          }
         
          if (!isset($selectTypes[$d['type']])){
            if (empty($d['row_id'])){        
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" of the option type "%2" is not defined', 'row_id', $d['type']));
              return;   
            }
            if (isset($rowIds[$templateId][$d['row_id']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with %1 "%2" for product #%3 has been already imported', 'row_id', $d['row_id'], $productId));
              return;       
            }
            $rowIds[$templateId][$d['row_id']] = 1;                       
          }          
                                         
                                     
          $type = $connection->quote($d['type']); 
          $isRequire = (int) $d['is_require'];  
          $sku = $connection->quote($d['sku']);        
          $maxCharacters   = !empty($d['max_characters']) ? (int) $d['max_characters'] : 'NULL';
          $fileExtension   = !empty($d['file_extension']) ? $connection->quote($d['file_extension']) : 'NULL';	          
          $imageSizeX      = (int) $d['image_size_x'];
          $imageSizeY      = (int) $d['image_size_y'];
          $sortOrder = (int) $d['sort_order']; 
          $title = $connection->quote($d['title']);		  
          $price = $connection->quote($d['price']);
          $priceType = $connection->quote($d['price_type']);
          $code = $connection->quote($d['code']);		   		        
          $rowId = !empty($d['row_id']) ? (int) $d['row_id'] : 'NULL';
          $layout = isset($layouts[$d['type']][$d['layout']]) ? $connection->quote($d['layout']) : "'above'";
          $popup = $connection->quote($d['popup']);
          $selectedByDeafault = $connection->quote($d['selected_by_default']); 
          $note = $connection->quote($d['note']);
          
          $toOTOT  .= ($toOTOT != '' ? ',' : '') . "({$nextOptionId},{$templateId},{$code},{$rowId},{$type},{$isRequire},{$sku},{$maxCharacters},{$fileExtension},{$imageSizeX},{$imageSizeY},{$sortOrder},{$layout},{$popup},{$selectedByDeafault})";
          $toOTOTT .= ($toOTOTT != '' ? ',' : '') . "({$nextOptionId},{$title})";	       
          if (!isset($selectTypes[$d['type']]))              
            $toOTOPT .= ($toOTOPT != '' ? ',' : '') . "({$nextOptionId},{$price},{$priceType})";       
          $toOTONT .= ($toOTONT != '' ? ',' : '') . "({$nextOptionId},{$note})"; 
                         

          $tIds[$templateId] = 1;        
          $nextOptionId++;
          
          $countRows++;
        }           


        if ($countRows > 0){ 
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_option')}` WHERE `template_id` IN (". implode(',', array_keys($tIds)) .")");		    	

          $codes = $connection->fetchCol("SELECT `code` FROM {$connection->getTableName('optionextended_template_option')}");																
          $duplicateCodes = array_intersect(array_keys($importedCodes), $codes);
          
          if (count($duplicateCodes) > 0){ 
            throw new \Magento\Framework\Exception\LocalizedException(__('Option code(s) "%1" already exist. Stop import process.', implode(", ", $duplicateCodes)));
            return; 
          } else {

            $connection->query($toOptionextendedTemplateOptionTable . $toOTOT);
            $connection->query($toOptionextendedTemplateOptionTitleTable . $toOTOTT);						
            if ($toOTOPT != '')			
              $connection->query($toOptionextendedTemplateOptionPriceTable . $toOTOPT);			  
            $connection->query($toOptionextendedTemplateOptionNoteTable . $toOTONT); 	
            
          } 
        }
                       
    }    
        
    
    

    
    
    
    public function importTemplateValues($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();
        
        $optionRows = $connection->fetchAssoc("SELECT code, option_id, template_id FROM {$connection->getTableName('optionextended_template_option')}");
				
        $r = $connection->fetchRow("SHOW TABLE STATUS LIKE '{$connection->getTableName('optionextended_template_value')}'");
        $nextValueId = $r['Auto_increment'];
		
        $toOptionextendedTemplateValueTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value')}` (`value_id`,`option_id`,`row_id`,`sku`,`sort_order`,`children`,`image`) VALUES ";
        $toOptionextendedTemplateValueTitleTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value_title')}` (`value_id`,`title`) VALUES ";
        $toOptionextendedTemplateValuePriceTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value_price')}` (`value_id`,`price`,`price_type`) VALUES ";
        $toOptionextendedTemplateValueDescriptionTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value_description')}` (`value_id`,`description`) VALUES ";

        $oIds   = array();
        $images = array();
        $rowIds = array(); 
        $toOTVT=$toOTVTT=$toOTVPT=$toOTVDT='';
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionRows[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionRows[$d['option_code']]['option_id'];             
            $templateId = $optionRows[$d['option_code']]['template_id'];  
            
			      if (empty($d['row_id'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'row_id'));
              return;       
            }
            
			      if (isset($rowIds[$templateId][$d['row_id']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with %1 "%2" for template #%3 has been already imported', 'row_id', $d['row_id'], $productId));
              return;              
            }            
            $rowIds[$templateId][$d['row_id']] = 1;

			      if (!isset($d['title']) || $d['title'] == ''){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'title'));
              return;            
            }
            
			      if (!empty($d['image']) && !isset($images[$d['image']])){
				      $image = substr($d['image'], 0, 1) != '/' ? '/' . $d['image'] : $d['image'];
				      $mediaPath = $this->_mediaDirectory->getAbsolutePath("catalog/product" . $image);	
				      if (!file_exists($mediaPath)) {																		
                throw new \Magento\Framework\Exception\LocalizedException(__('Image "%1" does not exist in the pub/media/catalog/product directory.'.$destinationPath, $d['image']));
                return;    		
				      }
              $images[$d['image']] = $image;				      
			      }                    
                    

            $sku = $connection->quote($d['sku']);
            $sortOrder = (int) $d['sort_order']; 
            $title = $connection->quote($d['title']);		  
            $price = $connection->quote($d['price']);
            $priceType = $connection->quote($d['price_type']);
            $rowId = (int) $d['row_id'];		     
            $children = $connection->quote($d['children']);
            $image = isset($images[$d['image']]) ? $connection->quote($images[$d['image']]) : "''";
            $description = $connection->quote($d['description']);

            $toOTVT  .= ($toOTVT != '' ? ',' : '') . "({$nextValueId},{$optionId},{$rowId},{$sku},{$sortOrder},{$children},{$image})";
            $toOTVTT .= ($toOTVTT != '' ? ',' : '') . "({$nextValueId},{$title})"; 
            $toOTVPT .= ($toOTVPT != '' ? ',' : '') . "({$nextValueId},{$price},{$priceType})";  
            $toOTVDT .= ($toOTVDT != '' ? ',' : '') . "({$nextValueId},{$description})";

            $oIds[$optionId] = 1;
            $nextValueId++;   
            
            $countRows++;
        }       

        if ($countRows > 0){     
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_value')}` WHERE `option_id` IN (" . implode(',', array_keys($oIds)) .")");		    	

          $connection->query($toOptionextendedTemplateValueTable . $toOTVT);
          $connection->query($toOptionextendedTemplateValueTitleTable . $toOTVTT);
          $connection->query($toOptionextendedTemplateValuePriceTable . $toOTVPT);
          $connection->query($toOptionextendedTemplateValueDescriptionTable . $toOTVDT);    	  	      
        }        
    }            




    
    public function importTemplateOptionsTranslate($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();

        $stores = $this->_storeManager->getStores(true, true);


        $optionIds = $connection->fetchPairs("SELECT code, option_id FROM {$connection->getTableName('optionextended_template_option')}");				

        $toOptionextendedTemplateOptionTitleTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option_title')}` (`option_id`,`store_id`,`title`) VALUES ";      
        $toOptionextendedTemplateOptionNoteTable = "INSERT INTO `{$connection->getTableName('optionextended_template_option_note')}` (`option_id`,`store_id`,`note`) VALUES ";

        $oIds   = array();
        $storeIds = array();        
        $toOTOTT=$toOTONT=''; 
        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionIds[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionIds[$d['option_code']];              
            
			      if (empty($d['store'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'store'));
              return;       
            }
            
			      if (!isset($stores[$d['store']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Store with code "%1" does not exist', $d['store']));
              return;              
            }           
            
            $storeId = $stores[$d['store']]->getId();            
             
			      if (isset($storeIds[$optionId][$storeId])){  
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" and store "%2" has been already imported', $d['option_code'], $d['store']));
              return;     
            }            
            
            $storeIds[$optionId][$storeId] = 1;

            if ($storeId == 0)
              continue;  

            if ($d['title'] != '')
              $toOTOTT .= ($toOTOTT != '' ? ',' : '') . "({$optionId},{$storeId},{$connection->quote($d['title'])})";
            if ($d['note'] != '')            	                         
              $toOTONT  .= ($toOTONT != '' ? ',' : '') . "({$optionId},{$storeId},{$connection->quote($d['note'])})"; 

            $oIds[$optionId]     = 1;
            
            $countRows++;
        }       

        if ($countRows > 0){
          $oIdsString = implode(',', array_keys($oIds));
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_option_title')}` WHERE `option_id` IN ({$oIdsString}) AND `store_id` != 0 ");		    	
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_option_note')}` WHERE `option_id` IN ({$oIdsString}) AND `store_id` != 0 ");	 	  	

          if ($toOTOTT != '')
			      $connection->query($toOptionextendedTemplateOptionTitleTable . $toOTOTT);	
          if ($toOTONT != '')			    										  
			      $connection->query($toOptionextendedTemplateOptionNoteTable . $toOTONT); 		    	      							       	  	      
        }
        
     
    }



    
    
    public function importTemplateValuesTranslate($rawData, $fieldNames)
    {
    
        $connection = $this->_resource->getConnection();

        $stores = $this->_storeManager->getStores(true, true);


        $optionIds = $connection->fetchPairs("SELECT code, option_id FROM {$connection->getTableName('optionextended_template_option')}");
        				        				
        $valueIds = array();
        $rs = $connection->fetchAll("SELECT option_id, row_id, value_id FROM {$connection->getTableName('optionextended_template_value')}");
        foreach ($rs as $r) 
          $valueIds[$r['option_id']][$r['row_id']] = $r['value_id'];
                    			
        $toOptionextendedTemplateValueTitleTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value_title')}` (`value_id`,`store_id`,`title`) VALUES ";
        $toOptionextendedTemplateValueDescriptionTable = "INSERT INTO `{$connection->getTableName('optionextended_template_value_description')}` (`value_id`,`store_id`,`description`) VALUES ";        
        
        $vIds = array();
        $storeIds = array();                
        $toOTVTT=$toOTVDT='';
        
        $otIds  = array();
        $oxvIds = array();
        $storeIds = array();                
        $toPOTTT=$toOVDT='';

        
        $countRows = 0;
        
        foreach ($rawData as $rowIndex => $csvData) {
          // skip headers
          if ($rowIndex == 0)
            continue;
      
          if (count($csvData) == 1 && $csvData[0] === null)
            continue;
                               
          $d = array();
          foreach ($fieldNames as $k => $v)
            $d[$v] = isset($csvData[$k]) ? $csvData[$k] : '';
                    
                    
                    
			      if (empty($d['option_code'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'option_code'));
              return;  
            }           

			      if (!isset($optionIds[$d['option_code']])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option with code "%1" does not exist', $d['option_code']));
              return;      
            }
            
            $optionId = $optionIds[$d['option_code']];           


			      if (empty($d['row_id'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'row_id'));
              return;       
            }
            
		        $rowId = (int) $d['row_id'];            

			      if (!isset($valueIds[$optionId][$rowId])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with row ID "%1" does not exist', $d['row_id']));
              return;      
            }

            $valueId = $valueIds[$optionId][$rowId];     
            
			      if (empty($d['store'])){
              throw new \Magento\Framework\Exception\LocalizedException(__('Required field "%1" is not defined', 'store'));
              return;       
            }
            
			      if (!isset($stores[$d['store']])){       
              throw new \Magento\Framework\Exception\LocalizedException(__('Store with code "%1" does not exist', $d['store']));
              return;              
            }           
            
            $storeId = $stores[$d['store']]->getId();            
             
			      if (isset($storeIds[$valueId][$storeId])){     
              throw new \Magento\Framework\Exception\LocalizedException(__('Option value with row_id "%1" and store "%2" for option code "%3" has been already imported', $d['row_id'], $d['store'], $d['option_code']));                  
              return;     
            }            
            
            $storeIds[$valueId][$storeId] = 1;

            if ($storeId == 0)
              continue;  

            if ($d['title'] != '')
              $toOTVTT .= ($toOTVTT != '' ? ',' : '') . "({$valueId},{$storeId},{$connection->quote($d['title'])})";
            if ($d['description'] != '')              	                         
              $toOTVDT  .= ($toOTVDT != '' ? ',' : '') . "({$valueId},{$storeId},{$connection->quote($d['description'])})"; 

            $vIds[$valueId] = 1; 
            
            $countRows++;
        }       

        if ($countRows > 0){
          $vIdsString = implode(',', array_keys($vIds));
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_value_title')}` WHERE `value_id` IN ({$vIdsString}) AND `store_id` != 0 ");		    	
          $connection->query("DELETE FROM `{$connection->getTableName('optionextended_template_value_description')}` WHERE `value_id` IN ({$vIdsString}) AND `store_id` != 0 ");	 	  	

          if ($toOTVTT != '')
	          $connection->query($toOptionextendedTemplateValueTitleTable . $toOTVTT);
          if ($toOTVDT != '')			    											  
	          $connection->query($toOptionextendedTemplateValueDescriptionTable . $toOTVDT); 
            	      							       	  	      
        }
        
     
    }



    
    
    

}
