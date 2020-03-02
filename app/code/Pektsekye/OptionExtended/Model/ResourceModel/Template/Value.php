<?php


namespace Pektsekye\OptionExtended\Model\ResourceModel\Template;

class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_storeManager;
    protected $_currencyFactory;
    protected $_config;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        $resourcePrefix = null          
    ) {
        $this->_currencyFactory = $currencyFactory;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        parent::__construct($context, $resourcePrefix);
    }
 
    
    public function _construct()
    {    
        $this->_init('optionextended_template_value', 'value_id');
    }

    
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
    
        $titleTable = $this->getTable('optionextended_template_value_title');
        $priceTable = $this->getTable('optionextended_template_value_price');
		    $descriptionTable = $this->getTable('optionextended_template_value_description');
        


        //title
        if (is_null($object->getTitleUseDefault())) {
            $statement = $this->getConnection()->select()
                ->from($titleTable)
                ->where('value_id = '.$object->getId().' and store_id = ?', 0);

            if ($this->getConnection()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->getConnection()->update(
                        $titleTable,
                            array('title' => $object->getTitle()),
                            $this->getConnection()->quoteInto('value_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->getConnection()->insert(
                    $titleTable,
                        array(
                            'value_id' => $object->getId(),
                            'store_id' => 0,
                            'title' => $object->getTitle()
                ));
            }
        }

        if ($object->getStoreId() != '0' && is_null($object->getTitleUseDefault())) {
            $statement = $this->getConnection()->select()
                ->from($titleTable)
                ->where('value_id = '.$object->getId().' and store_id = ?', $object->getStoreId());

            if ($this->getConnection()->fetchOne($statement)) {
                $this->getConnection()->update(
                    $titleTable,
                        array('title' => $object->getTitle()),
                        $this->getConnection()->quoteInto('value_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->getConnection()->insert(
                    $titleTable,
                        array(
                            'value_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'title' => $object->getTitle()
                ));
            }
        } elseif ($object->getTitleUseDefault() == 1) {
            $this->getConnection()->delete(
                $titleTable,
                $this->getConnection()->quoteInto('value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }



      // price
      if (is_null($object->getPriceUseDefault())) {
          $statement = $this->getConnection()->select()
              ->from($priceTable)
              ->where('value_id = '.$object->getId().' AND store_id = ?', 0);
          if ($this->getConnection()->fetchOne($statement)) {
              if ($object->getStoreId() == '0') {
                  $this->getConnection()->update(
                      $priceTable,
                      array(
                          'price' => $object->getPrice(),
                          'price_type' => $object->getPriceType()
                      ),
                      $this->getConnection()->quoteInto('value_id = '.$object->getId().' AND store_id = ?', 0)
                  );
              }
          } else {
              $this->getConnection()->insert(
                  $priceTable,
                  array(
                      'value_id' => $object->getId(),
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
                      ->where('value_id = '.$object->getId().' AND store_id = ?', $storeId);

                  if ($this->getConnection()->fetchOne($statement)) {
                      $this->getConnection()->update(
                          $priceTable,
                          array(
                              'price' => $newPrice,
                              'price_type' => $object->getPriceType()
                          ),
                          $this->getConnection()->quoteInto('value_id = '.$object->getId().' AND store_id = ?', $storeId)
                      );
                  } else {
                      $this->getConnection()->insert(
                          $priceTable,
                          array(
                              'value_id' => $object->getId(),
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
                $this->getConnection()->quoteInto('value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }              
      }



      // description		    		
        if (is_null($object->getDescriptionUseDefault())) {		
		      $statement = $this->getConnection()->select()
			      ->from($descriptionTable)
			      ->where('value_id = '.$object->getId().' AND store_id = ?', 0);

		      if ($this->getConnection()->fetchOne($statement)) {
			      if ($object->getStoreId() == '0') {
				      $this->getConnection()->update(
					      $descriptionTable,
						      array('description' => $object->getDescription()),
						      $this->getConnection()->quoteInto('value_id='.$object->getId().' AND store_id=?', 0)
				      );
			      }
		      } else {
			      $this->getConnection()->insert(
				      $descriptionTable,
					      array(
						      'value_id' => $object->getId(),
						      'store_id' => 0,
						      'description' => $object->getDescription()
			      ));
		      }
        }
        
		    if ($object->getStoreId() != '0' && is_null($object->getDescriptionUseDefault())) {
			    $statement = $this->getConnection()->select()
				    ->from($descriptionTable)
				    ->where('value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

			    if ($this->getConnection()->fetchOne($statement)) {;
				    $this->getConnection()->update(
					    $descriptionTable,
						    array('description' => $object->getDescription()),
						    $this->getConnection()->quoteInto('value_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
			    } else {
				    $this->getConnection()->insert(
					    $descriptionTable,
						    array(
							    'value_id' => $object->getId(),
							    'store_id' => $object->getStoreId(),
							    'description' => $object->getDescription()
				    ));
			    }
		    } elseif ($object->getDescriptionUseDefault() == 1){
            $this->getConnection()->delete(
                $descriptionTable,
                $this->getConnection()->quoteInto('value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );		    
		    }


        return parent::_afterSave($object);
    }      


    public function getStoreFields($oxOptionId, $storeId)
    {

        $titleTable = $this->getTable('optionextended_template_value_title');
        $priceTable = $this->getTable('optionextended_template_value_price');
		    $descriptionTable = $this->getTable('optionextended_template_value_description');
    

        $select = $this->getConnection()->select()
            ->from(array('default_title_table'=>$titleTable),array())
            ->joinLeft(array('store_title_table'=>$titleTable),
                "store_title_table.value_id=default_title_table.value_id AND store_title_table.store_id={$storeId}",
                array('store_title' => 'title', 'title' => new \Zend_Db_Expr('IFNULL(store_title_table.title, default_title_table.title)')))
                
            ->join(array('default_price_table' => $priceTable),
                "default_price_table.value_id=default_title_table.value_id AND default_price_table.store_id=0",array())
            ->joinLeft(array('store_price_table' => $priceTable),
                "store_price_table.value_id=default_price_table.value_id AND store_price_table.store_id={$storeId}",
                array('store_price' => 'price', 'price' => new \Zend_Db_Expr('IFNULL(store_price_table.price, default_price_table.price)'), 'price_type' => new \Zend_Db_Expr('IFNULL(store_price_table.price_type, default_price_table.price_type)')))
                
            ->join(array('default_description_table' => $descriptionTable),
                "default_description_table.value_id=default_title_table.value_id AND default_description_table.store_id=0",array())
            ->joinLeft(array('store_description_table' => $descriptionTable),
                "store_description_table.value_id=default_description_table.value_id AND store_description_table.store_id={$storeId}",
                array('store_description' => 'description', 'description' => new \Zend_Db_Expr('IFNULL(store_description_table.description, default_description_table.description)')))                                
                
            ->where("default_title_table.value_id={$oxOptionId} AND default_title_table.store_id=0");    
            
        return $this->getConnection()->fetchRow($select);
    }


    public function deleteValuesWithChidrenUpdate($templateId, $ids, $rowIds = array())
    {
      if (!is_null($ids)){
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(),'row_id')
            ->where('value_id IN (?)', $ids);
        $rowIds = $this->getConnection()->fetchCol($select); 
      }
      
      $select = $this->getConnection()->select()
          ->from(array('option_table'=>$this->getTable('optionextended_template_option')),array())        
          ->join(array('main_table'=>$this->getMainTable()), 'main_table.option_id = option_table.option_id', array('value_id', 'children'))
          ->where('option_table.template_id = ?', $templateId);
      $rows = $this->getConnection()->fetchAll($select);   

      foreach ($rows as $row){
        $children = explode(',', $row['children']);
        $childrenNew = array_diff($children, $rowIds);
        if (count($children) != count($childrenNew))
          $this->getConnection()->update($this->getMainTable(), array('children' => implode(',', $childrenNew)), 'value_id='.$row['value_id']);
      } 

      if (!is_null($ids))      
        $this->getConnection()->delete($this->getMainTable(), $this->getConnection()->quoteInto('value_id IN (?)', $ids));                                 
    } 


    public function getNextId()
    {
        $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$this->getTable('optionextended_template_value')}'");
        
        return (int) $r['Auto_increment'];
    }   


    public function getValuesCsv()
    {

      $headers = new \Magento\Framework\DataObject(array(     
        'option_code' => 'option_code',
        'row_id' => 'row_id',				  
        'title' => 'title',
        'price' => 'price',	
        'price_type' => 'price_type',			  
        'sku' => 'sku',	
        'sort_order' => 'sort_order',					
        'children' => 'children',		
        'image' => 'image',
        'description'	=> 'description' 			       
      ));
   
      $template = '"{{option_code}}","{{row_id}}","{{title}}","{{price}}","{{price_type}}","{{sku}}","{{sort_order}}","{{children}}","{{image}}","{{description}}"';		   
   		   
      $csv = $headers->toString($template) . "\n"; 					
     
      $data = $this->getConnection()->query("
          SELECT oxto.code as option_code,oxtv.row_id,oxtvt.title,oxtvp.price,oxtvp.price_type,oxtv.sku,oxtv.sort_order,oxtv.children,oxtv.image,oxtvd.description  
          FROM `{$this->getTable('optionextended_template_option')}` oxto 
          JOIN `{$this->getTable('optionextended_template_value')}` oxtv
            ON oxtv.option_id = oxto.option_id         
          LEFT JOIN  `{$this->getTable('optionextended_template_value_title')}` oxtvt 
            ON oxtvt.value_id = oxtv.value_id AND oxtvt.store_id = 0
          LEFT JOIN  `{$this->getTable('optionextended_template_value_price')}` oxtvp 
            ON oxtvp.value_id = oxtv.value_id AND oxtvp.store_id = 0
          LEFT JOIN  `{$this->getTable('optionextended_template_value_description')}` oxtvd 
            ON oxtvd.value_id = oxtv.value_id AND oxtvd.store_id = 0                                                                                          
      ");

      while ($row = $data->fetch()){   
        $row['description'] = str_replace('"', '""', $row['description']);         
        $rowObject = new \Magento\Framework\DataObject($row);
        $csv .= $rowObject->toString($template) . "\n";					      
      }
      
      return $csv;    
    }    
    	
	
    public function getValuesTranslateCsv()
    {

      $headers = new \Magento\Framework\DataObject(array(     
		    'option_code' => 'option_code',
		    'row_id' => 'row_id',		    
		    'store' => 'store',	
		    'title' => 'title',
		    'description' => 'description'        		       
      ));
      
   		$template = '"{{option_code}}","{{row_id}}","{{store}}","{{title}}","{{description}}"';   		 
 
      $csv = $headers->toString($template) . "\n"; 

      $data = $this->getConnection()->query("
        SELECT oxto.code as option_code,oxtv.row_id,cs.code as store,oxtvt.title,oxtvd.description  
        FROM `{$this->getTable('store')}` cs
        JOIN `{$this->getTable('optionextended_template_option')}` oxto
        JOIN `{$this->getTable('optionextended_template_value')}` oxtv 
          ON oxtv.option_id = oxto.option_id                  
        LEFT JOIN  `{$this->getTable('optionextended_template_value_title')}` oxtvt 
          ON oxtvt.value_id = oxtv.value_id AND oxtvt.store_id = cs.store_id
        LEFT JOIN  `{$this->getTable('optionextended_template_value_description')}` oxtvd 
          ON oxtvd.value_id = oxtv.value_id AND oxtvd.store_id = cs.store_id
        ORDER BY oxto.code,oxtv.row_id,cs.code                                                                                            
      ");

      while ($row = $data->fetch()){    
        $row['description'] = str_replace('"', '""', $row['description']);       
        $rowObject = new \Magento\Framework\DataObject($row);
        $csv .= $rowObject->toString($template) . "\n";					      
      }
      
      return $csv;    
    }  	     
       
}
