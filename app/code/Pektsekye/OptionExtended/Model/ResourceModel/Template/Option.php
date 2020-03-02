<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Template;

class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    protected $_storeManager;
    protected $_currencyFactory;
    protected $_config;
    protected $_oxTemplateValue; 
     
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Pektsekye\OptionExtended\Model\Template\Value $templateValue,
        $resourcePrefix = null                 
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_oxTemplateValue  = $templateValue;         
        parent::__construct($context, $resourcePrefix);
    }
    
    
    public function _construct()
    {    
        $this->_init('optionextended_template_option', 'option_id');
    }
    
    
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'code',
            'title' => __('Option with the same code')
        ));
        return $this;
    }

    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
    
        $titleTable = $this->getTable('optionextended_template_option_title');
        $priceTable = $this->getTable('optionextended_template_option_price');
		    $noteTable = $this->getTable('optionextended_template_option_note');
        


        //title
        if (is_null($object->getTitleUseDefault())) {
            $statement = $this->getConnection()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', 0);

            if ($this->getConnection()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->getConnection()->update(
                        $titleTable,
                            array('title' => $object->getTitle()),
                            $this->getConnection()->quoteInto('option_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->getConnection()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'title' => $object->getTitle()
                ));
            }
        }

        if ($object->getStoreId() != '0' && is_null($object->getTitleUseDefault())) {
            $statement = $this->getConnection()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', $object->getStoreId());

            if ($this->getConnection()->fetchOne($statement)) {
                $this->getConnection()->update(
                    $titleTable,
                        array('title' => $object->getTitle()),
                        $this->getConnection()->quoteInto('option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->getConnection()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'title' => $object->getTitle()
                ));
            }
        } elseif ($object->getTitleUseDefault() == 1) {
            $this->getConnection()->delete(
                $titleTable,
                $this->getConnection()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }



      // price
        if ($object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FILE ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DATE_TIME ||
            $object->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_TIME
        ) {

            //save for store_id = 0
            if (is_null($object->getPriceUseDefault())) {
                $statement = $this->getConnection()->select()
                    ->from($priceTable)
                    ->where('option_id = '.$object->getId().' AND store_id = ?', 0);
                if ($this->getConnection()->fetchOne($statement)) {
                    if ($object->getStoreId() == '0') {
                        $this->getConnection()->update(
                            $priceTable,
                            array(
                                'price' => $object->getPrice(),
                                'price_type' => $object->getPriceType()
                            ),
                            $this->getConnection()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', 0)
                        );
                    }
                } else {
                    $this->getConnection()->insert(
                        $priceTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'price' => $object->getPrice(),
                            'price_type' => $object->getPriceType()
                        )
                    );
                }
            }

            if (\Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE == (int) $this->_config->getValue(\Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE)){

              if ($object->getStoreId() != '0' && is_null($object->getPriceUseDefault())) {

                $baseCurrency = $this->_config->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    'default'
                );

                $storeIds = $this->_storeManager->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                if (is_array($storeIds)) {
                    foreach ($storeIds as $storeId) {
                        if ($object->getPriceType() == 'fixed') {
                            $storeCurrency = $this->_storeManager->getStore($storeId)->getBaseCurrencyCode();
                            $rate = $this->_currencyFactory->create()->load($baseCurrency)->getRate($storeCurrency);
                            if (!$rate) {
                                $rate=1;
                            }
                            $newPrice = $object->getPrice() * $rate;
                        } else {
                            $newPrice = $object->getPrice();
                        }
                        $statement = $this->getConnection()->select()
                            ->from($priceTable)
                            ->where('option_id = '.$object->getId().' AND store_id = ?', $storeId);

                        if ($this->getConnection()->fetchOne($statement)) {
                            $this->getConnection()->update(
                                $priceTable,
                                array(
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                ),
                                $this->getConnection()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $storeId)
                            );
                        } else {
                            $this->getConnection()->insert(
                                $priceTable,
                                array(
                                    'option_id' => $object->getId(),
                                    'store_id' => $storeId,
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                )
                            );
                        }
                    }// end foreach()
                }
              } elseif ($object->getPriceUseDefault() == 1) {
                  $this->getConnection()->delete(
                      $priceTable,
                      $this->getConnection()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
                  );
              }              
            }
        }


      // note		    		
        if (is_null($object->getNoteUseDefault())) {		
		      $statement = $this->getConnection()->select()
			      ->from($noteTable)
			      ->where('option_id = '.$object->getId().' AND store_id = ?', 0);

		      if ($this->getConnection()->fetchOne($statement)) {
			      if ($object->getStoreId() == '0') {
				      $this->getConnection()->update(
					      $noteTable,
						      array('note' => $object->getNote()),
						      $this->getConnection()->quoteInto('option_id='.$object->getId().' AND store_id=?', 0)
				      );
			      }
		      } else {
			      $this->getConnection()->insert(
				      $noteTable,
					      array(
						      'option_id' => $object->getId(),
						      'store_id' => 0,
						      'note' => $object->getNote()
			      ));
		      }
        }
        
		    if ($object->getStoreId() != '0' && is_null($object->getNoteUseDefault())) {
			    $statement = $this->getConnection()->select()
				    ->from($noteTable)
				    ->where('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

			    if ($this->getConnection()->fetchOne($statement)) {;
				    $this->getConnection()->update(
					    $noteTable,
						    array('note' => $object->getNote()),
						    $this->getConnection()->quoteInto('option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
			    } else {
				    $this->getConnection()->insert(
					    $noteTable,
						    array(
							    'option_id' => $object->getId(),
							    'store_id' => $object->getStoreId(),
							    'note' => $object->getNote()
				    ));
			    }
		    } elseif ($object->getNoteUseDefault() == 1){
            $this->getConnection()->delete(
                $noteTable,
                $this->getConnection()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );		    
		    }


        return parent::_afterSave($object);
    }      


    public function getStoreFields($oxOptionId, $storeId)
    {

        $titleTable = $this->getTable('optionextended_template_option_title');
        $priceTable = $this->getTable('optionextended_template_option_price');
		    $noteTable = $this->getTable('optionextended_template_option_note');
    

        $select = $this->getConnection()->select()
            ->from(array('default_title_table'=>$titleTable),array())
            ->joinLeft(array('store_title_table'=>$titleTable),
                "store_title_table.option_id=default_title_table.option_id AND store_title_table.store_id={$storeId}",
                array('store_title' => 'title', 'title' => new \Zend_Db_Expr('IFNULL(store_title_table.title, default_title_table.title)')))
                
            ->joinLeft(array('default_price_table' => $priceTable),
                "default_price_table.option_id=default_title_table.option_id AND default_price_table.store_id=0",array())
            ->joinLeft(array('store_price_table' => $priceTable),
                "store_price_table.option_id=default_price_table.option_id AND store_price_table.store_id={$storeId}",
                array('store_price' => 'price', 'price' => new \Zend_Db_Expr('IFNULL(store_price_table.price, default_price_table.price)'), 'price_type' => new \Zend_Db_Expr('IFNULL(store_price_table.price_type, default_price_table.price_type)')))
                
            ->join(array('default_note_table' => $noteTable),
                "default_note_table.option_id=default_title_table.option_id AND default_note_table.store_id=0",array())
            ->joinLeft(array('store_note_table' => $noteTable),
                "store_note_table.option_id=default_note_table.option_id AND store_note_table.store_id={$storeId}",
                array('store_note' => 'note', 'note' => new \Zend_Db_Expr('IFNULL(store_note_table.note, default_note_table.note)')))                                
                
            ->where("default_title_table.option_id={$oxOptionId} AND default_title_table.store_id=0");    
            
        return $this->getConnection()->fetchRow($select);
    }

    
    public function getLastRowId($templateId)
    {
          $id = (int) $this->getConnection()->fetchOne("
            SELECT MAX(row_id) as last_row_id 
            FROM  `{$this->getTable('optionextended_template_option')}`       
            WHERE template_id={$templateId} AND `type` IN ('field','area','file','date','date_time','time')   
            GROUP BY template_id
          ");

          $idV = (int) $this->getConnection()->fetchOne("
            SELECT MAX(oxtv.row_id) as last_row_id 
            FROM  `{$this->getTable('optionextended_template_option')}` oxto
            JOIN  `{$this->getTable('optionextended_template_value')}`  oxtv
              ON  oxtv.option_id = oxto.option_id
            WHERE template_id={$templateId}  
            GROUP BY template_id
          ");
          
      return max($id, $idV);
   }


    public function getNextId()
    {
        $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$this->getTable('optionextended_template_option')}'");
        
        return (int) $r['Auto_increment'];
    }

    
    public function getChildrenOptionData($templateId)
    {
        $select = $this->getConnection()->select()
            ->from(array('option_table'=>$this->getTable('optionextended_template_option')), array('option_id', 'row_id'))        
            ->join(array('option_title_table'=>$this->getTable('optionextended_template_option_title')),
             "option_title_table.option_id=option_table.option_id AND option_title_table.store_id=0",
              array('title'))                
            ->where("option_table.template_id = {$templateId}")
            ->order('sort_order', 'title');                 
            
        return $this->getConnection()->fetchAll($select);
   }


    public function getChildrenValueData($templateId)
    {
        $select = $this->getConnection()->select()
            ->from(array('option_table'=>$this->getTable('optionextended_template_option')), array('option_id'))                     
            ->join(array('value_table'=>$this->getTable('optionextended_template_value')),
                "value_table.option_id=option_table.option_id ", array('value_id', 'row_id', 'children'))            
            ->join(array('value_title_table'=>$this->getTable('optionextended_template_value_title')),
                "value_title_table.value_id=value_table.value_id AND value_title_table.store_id=0",
                array('title'))
                
            ->where("option_table.template_id = {$templateId}")
            ->order('value_table.sort_order', 'title');                  
            
        return $this->getConnection()->fetchAll($select);
   }



    public function getValueTitles($optionId)
    {
        $select = $this->getConnection()->select()
            ->from(array('value_table'=>$this->getTable('optionextended_template_value')), array('row_id'))            
            ->join(array('value_title_table'=>$this->getTable('optionextended_template_value_title')),
                "value_title_table.value_id=value_table.value_id AND value_title_table.store_id=0",
                array('title'))                
            ->where("value_table.option_id = {$optionId}")    
            ->order('sort_order', 'title'); 
                       
        return $this->getConnection()->fetchAll($select);
   }
   


  public function deleteOptionsWithChidrenUpdate($templateId, $ids)
  {    
    $select = $this->getConnection()->select()
        ->from($this->getMainTable(), 'row_id')       
        ->where("type IN ('field','area','file','date','date_time','time') AND option_id IN (?)", $ids);
    $rowIds = $this->getConnection()->fetchCol($select);
        
    $select = $this->getConnection()->select()
        ->from($this->getTable('optionextended_template_value'), 'row_id')
        ->where('option_id IN (?)', $ids);
    $rowIds = array_merge($rowIds, $this->getConnection()->fetchCol($select));

    $this->_oxTemplateValue->getResource()->deleteValuesWithChidrenUpdate((int) $templateId, null, $rowIds);

    $this->getConnection()->delete($this->getMainTable(), $this->getConnection()->quoteInto('option_id IN (?)', $ids));       
  } 


   public function deleteValuesWithChidrenUpdate($templateId, $optionId)
  {
    $select = $this->getConnection()->select()
        ->from($this->getMainTable(), 'row_id')       
        ->where("type IN ('field','area','file','date','date_time','time') AND option_id = ?", $optionId);
    $rowIds = $this->getConnection()->fetchCol($select);
        
    $select = $this->getConnection()->select()
        ->from($this->getTable('optionextended_template_value'), 'row_id')
        ->where('option_id = ?', $optionId);
    $rowIds = array_merge($rowIds, $this->getConnection()->fetchCol($select));

    $this->_oxTemplateValue->getResource()->deleteValuesWithChidrenUpdate((int) $templateId, null, $rowIds);
  }  
  
  
   public function deletePrice($optionId)
  {
    $this->getConnection()->delete(
      $this->getTable('optionextended_template_option_price'),
      $this->getConnection()->quoteInto('option_id = ?', $optionId)
    );  
  }   

  
   public function importOptionsFromProduct($templateId, $productId)
  {
		  
		  $cpT    = $this->getTable('catalog_product');
		  $cpoT   = $this->getTable('catalog_product_option');
		  $oxoT   = $this->getTable('optionextended_option');
		  $csT    = $this->getTable('store');
		  $cpotT  = $this->getTable('catalog_product_option_title');
		  $cpopT  = $this->getTable('catalog_product_option_price');
		  $oxonT  = $this->getTable('optionextended_option_note');
      $cpotvT = $this->getTable('catalog_product_option_type_value');
      $cpottT = $this->getTable('catalog_product_option_type_title');
      $cpotpT = $this->getTable('catalog_product_option_type_price');
      $oxvT   = $this->getTable('optionextended_value');
      $oxvdT  = $this->getTable('optionextended_value_description'); 
           		  	     			
      $oxtoT   = $this->getTable('optionextended_template_option');
      $oxtotT  = $this->getTable('optionextended_template_option_title');
      $oxtopT  = $this->getTable('optionextended_template_option_price');
      $oxtonT  = $this->getTable('optionextended_template_option_note');                   
      $oxtvT   = $this->getTable('optionextended_template_value'); 
      $oxtvtT  = $this->getTable('optionextended_template_value_title');
      $oxtvpT  = $this->getTable('optionextended_template_value_price');
      $oxtvdT  = $this->getTable('optionextended_template_value_description');

             			
      $oResult = $this->getConnection()->fetchAll("
        SELECT po.option_id,type,is_require,sku,max_characters,file_extension,image_size_x,image_size_y,sort_order,
               row_id,layout,popup,selected_by_default
        FROM `{$cpoT}` po 
        JOIN `{$oxoT}` oxo
          ON oxo.option_id = po.option_id
        WHERE po.product_id={$productId}    
      ");

      $options = array();
      foreach ($oResult as $r){ 
        $maxCharacters   = !is_null($r['max_characters']) ? $r['max_characters'] : 'NULL';
        $fileExtension   = !is_null($r['file_extension']) ? $this->getConnection()->quote($r['file_extension']) : 'NULL';	          
        $imageSizeX      = (int) $r['image_size_x'];
        $imageSizeY      = (int) $r['image_size_y'];
        $rowId           = !is_null($r['row_id']) ? $r['row_id'] : 'NULL';               
        $options[$r['option_id']]  = "{$rowId},'{$r['type']}',{$r['is_require']},{$this->getConnection()->quote($r['sku'])},{$maxCharacters},{$fileExtension},{$imageSizeX},{$imageSizeY},{$r['sort_order']},'{$r['layout']}','{$r['popup']}','{$r['selected_by_default']}')"; 
      }     
      unset($oResult);
 
	    if (count($options) > 0){
         
        $otResult = $this->getConnection()->fetchAll("
          SELECT po.option_id,cs.store_id,title,price,price_type,note
          FROM `{$cpoT}` po 
          JOIN `{$oxoT}` oxo 
            ON oxo.option_id = po.option_id
          JOIN `{$csT}` cs  
          LEFT JOIN  `{$cpotT}` pot 
            ON pot.option_id = po.option_id AND pot.store_id = cs.store_id
          LEFT JOIN  `{$cpopT}` pop 
            ON pop.option_id = po.option_id AND pop.store_id = cs.store_id
          LEFT JOIN  `{$oxonT}` oxon 
            ON oxon.ox_option_id = oxo.ox_option_id AND oxon.store_id = cs.store_id                           
          WHERE po.product_id={$productId}    
        ");

        $oTitles = array();
        $oPrices = array();
        $oNotes = array();

        foreach ($otResult as $r){
         if (!is_null($r['title']) || $r['store_id'] == 0)     
          $oTitles[$r['option_id']][] = array('store_id'=>$r['store_id'], 'title'=>$this->getConnection()->quote($r['title']));
         if (!is_null($r['price']) || $r['store_id'] == 0)     
          $oPrices[$r['option_id']][] = array('store_id'=>$r['store_id'], 'price'=>(float) $r['price'], 'price_type'=>$r['price_type']); 
         if (!is_null($r['note']) || $r['store_id'] == 0)     
          $oNotes[$r['option_id']][] = array('store_id'=>$r['store_id'], 'note'=>$this->getConnection()->quote($r['note']));                      
        }  
        unset($otResult);

        $ovResult = $this->getConnection()->fetchAll("
          SELECT po.option_id,potv.option_type_id,potv.sku,potv.sort_order,
                 oxv.row_id,children,image
          FROM `{$cpoT}` po 
          JOIN `{$oxoT}` oxo ON oxo.option_id = po.option_id 
          JOIN `{$cpotvT}` potv ON potv.option_id = po.option_id
          JOIN `{$oxvT}` oxv ON oxv.option_type_id = potv.option_type_id                                  
          WHERE po.product_id={$productId}    
        ");
   
        $values = array();      
        foreach ($ovResult as $r)
          $values[$r['option_id']][$r['option_type_id']] = "{$r['row_id']},{$this->getConnection()->quote($r['sku'])},{$r['sort_order']},{$this->getConnection()->quote($r['children'])},{$this->getConnection()->quote($r['image'])})";           
                       
        unset($ovResult);
        
        $ovtResult = $this->getConnection()->fetchAll("
          SELECT potv.option_type_id,po.option_id,cs.store_id,title,price,price_type,description
          FROM `{$cpoT}` po 
          JOIN `{$oxoT}` oxo 
            ON oxo.option_id = po.option_id 
          JOIN `{$cpotvT}` potv 
            ON potv.option_id = po.option_id
          JOIN `{$oxvT}` oxv 
            ON oxv.option_type_id = potv.option_type_id 
          JOIN  `{$csT}` cs        
          LEFT JOIN  `{$cpottT}` pott 
            ON pott.option_type_id = potv.option_type_id AND pott.store_id = cs.store_id
          LEFT JOIN  `{$cpotpT}` potp 
            ON potp.option_type_id = potv.option_type_id AND potp.store_id = cs.store_id
          LEFT JOIN  `{$oxvdT}` oxvd 
            ON oxvd.ox_value_id = oxv.ox_value_id AND oxvd.store_id = cs.store_id                                                
          WHERE po.product_id={$productId}
        ");
        
        $ovTitles = array();
        $ovPrices = array();
        $ovDescriptions = array(); 
          
        foreach ($ovtResult as $r){
         if (!is_null($r['title']) || $r['store_id'] == 0)     
          $ovTitles[$r['option_type_id']][] = "{$r['store_id']},{$this->getConnection()->quote($r['title'])})";
         if (!is_null($r['price']) || $r['store_id'] == 0){
          $price = (float) $r['price'];     
          $ovPrices[$r['option_type_id']][] = "{$r['store_id']},{$price},'{$r['price_type']}')";
         } 
         if (!is_null($r['description']) || $r['store_id'] == 0)     
          $ovDescriptions[$r['option_type_id']][] = "{$r['store_id']},{$this->getConnection()->quote($r['description'])})";                               
        }
        unset($ovtResult);        
        

        $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$oxtoT}'");
        $nextOptionId = $r['Auto_increment'];
        $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$oxtvT}'");
        $nextValueId = $r['Auto_increment'];	

        $toOptionTable           = "INSERT INTO `{$oxtoT}`  (`option_id`,`template_id`,`code`,`row_id`,`type`,`is_require`,`sku`,`max_characters`,`file_extension`,`image_size_x`,`image_size_y`,`sort_order`,`layout`,`popup`,`selected_by_default`) VALUES	";      
        $toOptionTitleTable      = "INSERT INTO `{$oxtotT}` (`option_id`,`store_id`,`title`) VALUES	";      
        $toOptionPriceTable      = "INSERT INTO `{$oxtopT}` (`option_id`,`store_id`,`price`,`price_type`) VALUES ";
        $toOptionNoteTable       = "INSERT INTO `{$oxtonT}` (`option_id`,`store_id`,`note`) VALUES ";
        $toValueTable            = "INSERT INTO `{$oxtvT}`  (`value_id`,`option_id`,`row_id`,`sku`,`sort_order`,`children`,`image`) VALUES ";
        $toValueTitleTable       = "INSERT INTO `{$oxtvtT}` (`value_id`,`store_id`,`title`) VALUES ";
        $toValuePriceTable       = "INSERT INTO `{$oxtvpT}` (`value_id`,`store_id`,`price`,`price_type`) VALUES ";
        $toValueDescriptionTable = "INSERT INTO `{$oxtvdT}` (`value_id`,`store_id`,`description`) VALUES ";


        $toOT=$toOTT=$toOPT=$toONT=$toVT=$toVTT=$toVPT=$toVDT='';
       
        $haveOptionValues = false; 


        foreach($options as $id => $r){	 
  
          $toOT .= ($toOT != '' ? ',' : '') . "({$nextOptionId},{$templateId},'opt-{$templateId}-{$nextOptionId}',{$r}";            
          foreach ($oTitles[$id] as $k => $v)
            $toOTT .= ($toOTT != '' ? ',' : '') . "({$nextOptionId},{$v['store_id']},{$v['title']})";

          foreach ($oPrices[$id] as $k => $v)              	      
            $toOPT .= ($toOPT != '' ? ',' : '') . "({$nextOptionId},{$v['store_id']},{$v['price']},'{$v['price_type']}')";
    
          foreach ($oNotes[$id] as $k => $v)                                     
            $toONT .= ($toONT != '' ? ',' : '') . "({$nextOptionId},{$v['store_id']},{$v['note']})";
                              
          if (isset($values[$id])){           
            foreach ($values[$id] as $k => $v){	              
              $toVT .= ($toVT != '' ? ',' : '') . "({$nextValueId},{$nextOptionId},{$v}";
              foreach ($ovTitles[$k] as $vv)                
                $toVTT .= ($toVTT != '' ?',' : '')  . "({$nextValueId},{$vv}";             
              foreach ($ovPrices[$k] as $vv)                   
                $toVPT .= ($toVPT != '' ? ',' : '') . "({$nextValueId},{$vv}";              
              foreach ($ovDescriptions[$k] as $vv)                  
                $toVDT .= ($toVDT != '' ? ',' : '') . "({$nextValueId},{$vv}";                                                             
              $nextValueId++;	    	    		      	  
	          }           	
	          $haveOptionValues = true;
          }
		                    
          $nextOptionId++;	
        }	  

        $this->getConnection()->query($toOptionTable . $toOT);
        $this->getConnection()->query($toOptionTitleTable . $toOTT);              
        $this->getConnection()->query($toOptionPriceTable . $toOPT);
        $this->getConnection()->query($toOptionNoteTable . $toONT);
                    
        if ($haveOptionValues){      
          $this->getConnection()->query($toValueTable . $toVT);
          $this->getConnection()->query($toValueTitleTable . $toVTT);
          $this->getConnection()->query($toValuePriceTable . $toVPT);
          $this->getConnection()->query($toValueDescriptionTable . $toVDT);
        }				
     
    } 
    
  }   


  public function duplicate($optionId)
  {

      $toOT=$toOTT=$toOPT=$toONT=$toVT=$toVTT=$toVPT=$toVDT='';
      $haveOptionPrices = $haveOptionValues = false; 
      $newRowIds = array();
		  
		  $csT    = $this->getTable('store');
           		  	     			
      $oxtoT   = $this->getTable('optionextended_template_option');
      $oxtotT  = $this->getTable('optionextended_template_option_title');
      $oxtopT  = $this->getTable('optionextended_template_option_price');
      $oxtonT  = $this->getTable('optionextended_template_option_note');                   
      $oxtvT   = $this->getTable('optionextended_template_value'); 
      $oxtvtT  = $this->getTable('optionextended_template_value_title');
      $oxtvpT  = $this->getTable('optionextended_template_value_price');
      $oxtvdT  = $this->getTable('optionextended_template_value_description');
      
      $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$oxtoT}'");
      $nextOptionId = $r['Auto_increment'];
      $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$oxtvT}'");
      $nextValueId = $r['Auto_increment'];	
                     			
      $oResult = $this->getConnection()->fetchRow("
        SELECT option_id,template_id,type,is_require,sku,max_characters,file_extension,image_size_x,image_size_y,sort_order,
               row_id,layout,popup,selected_by_default
        FROM `{$oxtoT}`
        WHERE option_id={$optionId}    
      ");
      
      if (!empty($oResult)){

        $templateId = (int) $oResult['template_id'];       
        $lastRowId  = $this->getLastRowId($templateId);
                  
        $otResult = $this->getConnection()->fetchAll("
          SELECT cs.store_id,title,price,price_type,note
          FROM `{$oxtoT}` oxt 
          JOIN `{$csT}` cs     
          LEFT JOIN  `{$oxtotT}` oxtt 
            ON oxtt.option_id = oxt.option_id AND oxtt.store_id = cs.store_id
          LEFT JOIN  `{$oxtopT}` oxtp 
            ON oxtp.option_id = oxt.option_id AND oxtp.store_id = cs.store_id
          LEFT JOIN  `{$oxtonT}` oxtn 
            ON oxtn.option_id = oxt.option_id AND oxtn.store_id = cs.store_id                           
          WHERE oxt.option_id={$optionId}    
        ");

        foreach ($otResult as $r){
         if (!is_null($r['title']))     
          $toOTT .= ($toOTT != '' ? ',' : '') . "({$nextOptionId},{$r['store_id']},{$this->getConnection()->quote($r['title'])})";
         if (!is_null($r['price'])){     
          $toOPT .= ($toOPT != '' ? ',' : '') . "({$nextOptionId},{$r['store_id']},'{$r['price']}','{$r['price_type']}')"; 
          $haveOptionPrices = true;
         } 
         if (!is_null($r['note']))     
          $toONT .= ($toONT != '' ? ',' : '') . "({$nextOptionId},{$r['store_id']},{$this->getConnection()->quote($r['note'])})";                      
        }
       
        unset($otResult);

        $ovResult = $this->getConnection()->fetchAll("
          SELECT option_id,value_id,sku,sort_order,
                 row_id,children,image
          FROM `{$oxtvT}`                                 
          WHERE option_id={$optionId}    
        ");
   
        $values = array();      
        foreach ($ovResult as $r){
          $rowId = $lastRowId + 1;
          $newRowIds[$r['row_id']] = $rowId;           
          $values[$r['value_id']] = "{$rowId},{$this->getConnection()->quote($r['sku'])},{$r['sort_order']},{$this->getConnection()->quote($r['image'])})";           
          $lastRowId++;
        }            
        unset($ovResult);
    
        $vResult = $this->getConnection()->fetchAll("
          SELECT v.value_id,v.option_id,cs.store_id,title,price,price_type,description
          FROM `{$oxtvT}` v 
          JOIN  `{$csT}` cs        
          LEFT JOIN  `{$oxtvtT}` vt 
            ON vt.value_id = v.value_id AND vt.store_id = cs.store_id
          LEFT JOIN  `{$oxtvpT}` vp 
            ON vp.value_id = v.value_id AND vp.store_id = cs.store_id
          LEFT JOIN  `{$oxtvdT}` vd 
            ON vd.value_id = v.value_id AND vd.store_id = cs.store_id                                                
          WHERE v.option_id={$optionId}
        ");
   
        $ovTitles = array();
        $ovPrices = array();
        $ovDescriptions = array(); 
            
        foreach ($vResult as $r){
         if (!is_null($r['title']))     
          $ovTitles[$r['value_id']][] = "{$r['store_id']},{$this->getConnection()->quote($r['title'])})";
         if (!is_null($r['price']))     
          $ovPrices[$r['value_id']][] = "{$r['store_id']},{$r['price']},'{$r['price_type']}')";
         if (!is_null($r['description']))     
          $ovDescriptions[$r['value_id']][] = "{$r['store_id']},{$this->getConnection()->quote($r['description'])})";                               
        }
        unset($vResult);


        $maxCharacters   = !is_null($oResult['max_characters']) ? $oResult['max_characters'] : 'NULL';
        $fileExtension   = !is_null($oResult['file_extension']) ? $this->getConnection()->quote($oResult['file_extension']) : 'NULL';	          
        $imageSizeX      = (int) $oResult['image_size_x'];
        $imageSizeY      = (int) $oResult['image_size_y'];
     
        $rowId = 'NULL';
        if (!is_null($oResult['row_id'])){
          $rowId = $lastRowId + 1;
          $newRowIds[$oResult['row_id']] = $rowId;
          $lastRowId++;
        }  

        $sd = '';
        if ($oResult['selected_by_default'] != '')        
          foreach (explode(',', $oResult['selected_by_default']) as $id)
            $sd .= ($sd != '' ? ',' : '') . $newRowIds[$id];
                                                  
        $toOT = "({$nextOptionId},{$templateId},'opt-{$templateId}-{$nextOptionId}',{$rowId},'{$oResult['type']}',{$oResult['is_require']},{$this->getConnection()->quote($oResult['sku'])},{$maxCharacters},{$fileExtension},{$imageSizeX},{$imageSizeY},{$oResult['sort_order']},'{$oResult['layout']}','{$oResult['popup']}','{$sd}')";        
        unset($oResult);
                                        
        if (count($values) > 0){           
          foreach ($values as $k => $v){	              
            $toVT .= ($toVT != '' ? ',' : '') . "({$nextValueId},{$nextOptionId},{$v}";
            foreach ($ovTitles[$k] as $vv)                
              $toVTT .= ($toVTT != '' ?',' : '')  . "({$nextValueId},{$vv}";                 
            foreach ($ovPrices[$k] as $vv)                   
              $toVPT .= ($toVPT != '' ? ',' : '') . "({$nextValueId},{$vv}";                
            foreach ($ovDescriptions[$k] as $vv)                  
              $toVDT .= ($toVDT != '' ? ',' : '') . "({$nextValueId},{$vv}";                                                             
            $nextValueId++;	    	    		      	  
          }           	
          $haveOptionValues = true;
        }
		                      
        $toOptionTable           = "INSERT INTO `{$oxtoT}`  (`option_id`,`template_id`,`code`,`row_id`,`type`,`is_require`,`sku`,`max_characters`,`file_extension`,`image_size_x`,`image_size_y`,`sort_order`,`layout`,`popup`,`selected_by_default`) VALUES	";      
        $toOptionTitleTable      = "INSERT INTO `{$oxtotT}` (`option_id`,`store_id`,`title`) VALUES	";      
        $toOptionPriceTable      = "INSERT INTO `{$oxtopT}` (`option_id`,`store_id`,`price`,`price_type`) VALUES ";
        $toOptionNoteTable       = "INSERT INTO `{$oxtonT}` (`option_id`,`store_id`,`note`) VALUES ";
        $toValueTable            = "INSERT INTO `{$oxtvT}`  (`value_id`,`option_id`,`row_id`,`sku`,`sort_order`,`image`) VALUES ";
        $toValueTitleTable       = "INSERT INTO `{$oxtvtT}` (`value_id`,`store_id`,`title`) VALUES ";
        $toValuePriceTable       = "INSERT INTO `{$oxtvpT}` (`value_id`,`store_id`,`price`,`price_type`) VALUES ";
        $toValueDescriptionTable = "INSERT INTO `{$oxtvdT}` (`value_id`,`store_id`,`description`) VALUES ";

        $this->getConnection()->query($toOptionTable . $toOT);
        $this->getConnection()->query($toOptionTitleTable . $toOTT);
        if ($haveOptionPrices)	              
          $this->getConnection()->query($toOptionPriceTable . $toOPT);
        $this->getConnection()->query($toOptionNoteTable . $toONT);
                    
        if ($haveOptionValues){         
          $this->getConnection()->query($toValueTable . $toVT);
          $this->getConnection()->query($toValueTitleTable . $toVTT);
          $this->getConnection()->query($toValuePriceTable . $toVPT);
          $this->getConnection()->query($toValueDescriptionTable . $toVDT);
        }				
     
    } 
    
    return $nextOptionId;
 
  }      
  

  public function getValueCount($optionId)
  {
      $select = $this->getConnection()
          ->select()         
          ->from($this->getTable('optionextended_template_value'), 'COUNT(value_id)')
          ->where('option_id = ?', $optionId)
          ->group('option_id');
      return $this->getConnection()->fetchOne($select);   

  } 
     
     
  public function getGridValueCount($templateId)
  {
      $select = $this->getConnection()
          ->select()
          ->from(array('option_table'=>$this->getTable('optionextended_template_option')), 'option_id')            
          ->join(array('value_table'=>$this->getTable('optionextended_template_value')), 
            'value_table.option_id = option_table.option_id',
            'COUNT(value_id)')
          ->where('template_id = ?', $templateId)
          ->group('option_table.option_id');
      return $this->getConnection()->fetchPairs($select);   

  } 


  public function getOptionsCsv()
  {

    $headers = new \Magento\Framework\DataObject(array(
        'template_code' => 'template_code',
        'code' => 'code',		    				
        'title' => 'title',
        'type' => 'type',
        'is_require' => 'is_require',
        'sort_order' => 'sort_order',				
        'note' => 'note',	
        'layout' => 'layout',			
        'popup' => 'popup',						
        'price' => 'price',	
        'price_type' => 'price_type',	
        'sku' => 'sku',
        'max_characters' => 'max_characters',
        'file_extension' => 'file_extension',
        'image_size_x' => 'image_size_x',
        'image_size_y' => 'image_size_y',			  		
        'row_id' => 'row_id',
        'selected_by_default'	=> 'selected_by_default'	        
    ));
 
    $template = '"{{template_code}}","{{code}}","{{title}}","{{type}}","{{is_require}}","{{sort_order}}","{{note}}","{{layout}}","{{popup}}","{{price}}","{{price_type}}","{{sku}}","{{max_characters}}","{{file_extension}}","{{image_size_x}}","{{image_size_y}}","{{row_id}}","{{selected_by_default}}"';		   
      
    $csv = $headers->toString($template) . "\n"; 					
   
    $data = $this->getConnection()->query("
        SELECT oxt.code as template_code,oxto.code,oxtot.title,oxto.type,oxto.is_require,oxto.sort_order,oxton.note,oxto.layout,oxto.popup,oxtop.price,oxtop.price_type,oxto.sku,oxto.max_characters,oxto.file_extension,oxto.image_size_x,oxto.image_size_y,oxto.row_id,oxto.selected_by_default
        FROM `{$this->getTable('optionextended_template')}` oxt
        JOIN `{$this->getTable('optionextended_template_option')}` oxto
          ON oxto.template_id = oxt.template_id            
        LEFT JOIN  `{$this->getTable('optionextended_template_option_title')}` oxtot 
          ON oxtot.option_id = oxto.option_id AND oxtot.store_id = 0
        LEFT JOIN  `{$this->getTable('optionextended_template_option_price')}` oxtop 
          ON oxtop.option_id = oxto.option_id AND oxtop.store_id = 0
        LEFT JOIN  `{$this->getTable('optionextended_template_option_note')}` oxton 
          ON oxton.option_id = oxto.option_id AND oxton.store_id = 0                                                                                         
    ");

    while ($row = $data->fetch()){  
      $row['note'] = str_replace('"', '""', $row['note']);  
      $rowObject = new \Magento\Framework\DataObject($row);
      $csv .= $rowObject->toString($template) . "\n";					      
    }
    
    return $csv;    
  }    
  
  
  public function getOptionsTranslateCsv()
  {

    $headers = new \Magento\Framework\DataObject(array(	
      'option_code' => 'option_code',
      'store' => 'store',	
      'title' => 'title',
      'note' 	=> 'note'		            
    ));
    
    $template = '"{{option_code}}","{{store}}","{{title}}","{{note}}"';		 

    $csv = $headers->toString($template) . "\n"; 			
   
    $data = $this->getConnection()->query("
      SELECT oxto.code as option_code,cs.code as store,oxtot.title,oxton.note
      FROM `{$this->getTable('store')}` cs     
      JOIN `{$this->getTable('optionextended_template_option')}` oxto            
      LEFT JOIN  `{$this->getTable('optionextended_template_option_title')}` oxtot 
        ON oxtot.option_id = oxto.option_id AND oxtot.store_id = cs.store_id
      LEFT JOIN  `{$this->getTable('optionextended_template_option_note')}` oxton 
        ON oxton.option_id = oxto.option_id AND oxton.store_id = cs.store_id    
      ORDER BY oxto.code,cs.code                                                                                        
    ");

    while ($row = $data->fetch()){   
      $row['note'] = str_replace('"', '""', $row['note']);        
      $rowObject = new \Magento\Framework\DataObject($row);
      $csv .= $rowObject->toString($template) . "\n";					      
    }
    
    return $csv;    
  }   
  

    
}
