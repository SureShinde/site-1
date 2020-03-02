<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel;

class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

  protected $_oxValue;
  protected $_oxTemplate;  
  
  public function __construct(
      \Magento\Framework\Model\ResourceModel\Db\Context $resource,  
      \Pektsekye\OptionExtended\Model\Value $oxValue,
      \Pektsekye\OptionExtended\Model\Template $oxTemplate      
  ) {
      $this->_oxValue = $oxValue;
      $this->_oxTemplate = $oxTemplate;              
      parent::__construct($resource);
  } 


  public function _construct()
  {    
    $this->_init('optionextended_option', 'ox_option_id');
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
    $noteTable = $this->getTable('optionextended_option_note');
        
    if (!$object->getData('scope', 'optionextended_note')) {		
      $statement = $this->getConnection()->select()
        ->from($noteTable)
        ->where('ox_option_id = '.$object->getId().' AND store_id = ?', 0);

      if ($this->getConnection()->fetchOne($statement)) {
        if ($object->getStoreId() == '0') {
          $this->getConnection()->update(
            $noteTable,
              array('note' => $object->getNote()),
              $this->getConnection()->quoteInto('ox_option_id='.$object->getId().' AND store_id=?', 0)
          );
        }
      } else {
        $this->getConnection()->insert(
          $noteTable,
            array(
              'ox_option_id' => $object->getId(),
              'store_id' => 0,
              'note' => $object->getNote()
        ));
      }
    }
    
    if ($object->getStoreId() != '0' && !$object->getData('scope', 'optionextended_note')) {
      $statement = $this->getConnection()->select()
        ->from($noteTable)
        ->where('ox_option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

      if ($this->getConnection()->fetchOne($statement)) {;
        $this->getConnection()->update(
          $noteTable,
            array('note' => $object->getNote()),
            $this->getConnection()->quoteInto('ox_option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
      } else {
        $this->getConnection()->insert(
          $noteTable,
            array(
              'ox_option_id' => $object->getId(),
              'store_id' => $object->getStoreId(),
              'note' => $object->getNote()
        ));
      }
    } elseif ($object->getData('scope', 'optionextended_note')){
        $this->getConnection()->delete(
            $noteTable,
            $this->getConnection()->quoteInto('ox_option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
        );		    
    }
  }



  public function duplicate($oldProductId, $newProductId)
  {
      $this->_oxTemplate->getResource()->applyOptionTemplatesToDuplicatedProduct((int) $oldProductId, (int) $newProductId);								      
 
      $noteTable = $this->getTable('optionextended_option_note');
    
      $r = $this->getConnection()->fetchRow("SHOW TABLE STATUS LIKE '{$this->getMainTable()}'");
      $nextOxOptionId = $r['Auto_increment'];		  
      
      // read and prepare original product options
      $select = $this->getConnection()->select()
          ->from($this->getTable('catalog_product_option'), 'option_id')
          ->where('product_id=?', $oldProductId);
      $oldOptionIds = $this->getConnection()->fetchCol($select);

      $select = $this->getConnection()->select()
          ->from($this->getTable('catalog_product_option'), 'option_id')
          ->where('product_id=?', $newProductId);
      $newOptionIds = $this->getConnection()->fetchCol($select);
    
      foreach ($oldOptionIds as $ind => $oldOptionId) {

      // read and prepare original optionextended options
        $select = $this->getConnection()->select()
          ->from($this->getMainTable())
          ->where('option_id=?', $oldOptionId);
        $row = $this->getConnection()->fetchRow($select);
        
        if (empty($row)) //original product does not have optionextended data
          continue;
                
        $oldOxOptionId = $row['ox_option_id'];
        $row['option_id'] = $newOptionIds[$ind];						
        $row['product_id'] = $newProductId;
        $row['code'] = "opt-{$newProductId}-{$nextOxOptionId}";				
        unset($row['ox_option_id']);

      // insert optionextended options to duplicated option
        $this->getConnection()->insert($this->getMainTable(), $row);

      // copy optionextended options note
        $sql = 'REPLACE INTO `' . $noteTable . '` '
           . 'SELECT NULL, ' . $nextOxOptionId . ', `store_id`, `note`'
           . 'FROM `' . $noteTable . '` WHERE `ox_option_id`=' . $oldOxOptionId;
        $this->getConnection()->query($sql);
      
        $this->_oxValue->getResource()->duplicate($oldOptionId, $newOptionIds[$ind], $newProductId);
        $nextOxOptionId++;
      }

  }



 
    public function getOptionsCsv()
    {

      $headers = new \Magento\Framework\DataObject(array(
		      'product_sku' => 'product_sku',
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
   
		  $template = '"{{product_sku}}","{{code}}","{{title}}","{{type}}","{{is_require}}","{{sort_order}}","{{note}}","{{layout}}","{{popup}}","{{price}}","{{price_type}}","{{sku}}","{{max_characters}}","{{file_extension}}","{{image_size_x}}","{{image_size_y}}","{{row_id}}","{{selected_by_default}}"';		   
        
      $csv = $headers->toString($template) . "\n"; 						
     
      $data = $this->getConnection()->query("
        SELECT p.sku as product_sku,oxo.code,pot.title,po.type,po.is_require,po.sort_order,oxon.note,oxo.layout,oxo.popup,pop.price,pop.price_type,po.sku,po.max_characters,po.file_extension,po.image_size_x,po.image_size_y,oxo.row_id,oxo.selected_by_default
        FROM `{$this->getTable('catalog_product_entity')}` p
        JOIN `{$this->getTable('catalog_product_option')}` po
          ON po.product_id = p.entity_id            
        JOIN `{$this->getTable('optionextended_option')}` oxo 
          ON oxo.option_id = po.option_id 
        LEFT JOIN  `{$this->getTable('catalog_product_option_title')}` pot 
          ON pot.option_id = po.option_id AND pot.store_id = 0
        LEFT JOIN  `{$this->getTable('catalog_product_option_price')}` pop 
          ON pop.option_id = po.option_id AND pop.store_id = 0
        LEFT JOIN  `{$this->getTable('optionextended_option_note')}` oxon 
          ON oxon.ox_option_id = oxo.ox_option_id AND oxon.store_id = 0                                                                                         
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
        SELECT oxo.code as option_code,cs.code as store,pot.title,oxon.note
        FROM `{$this->getTable('store')}` cs     
        JOIN `{$this->getTable('optionextended_option')}` oxo            
        LEFT JOIN  `{$this->getTable('catalog_product_option_title')}` pot 
          ON pot.option_id = oxo.option_id AND pot.store_id = cs.store_id
        LEFT JOIN  `{$this->getTable('optionextended_option_note')}` oxon 
          ON oxon.ox_option_id = oxo.ox_option_id AND oxon.store_id = cs.store_id    
        ORDER BY oxo.code,cs.code                                                                                          
      ");

      while ($row = $data->fetch()){   
        $row['note'] = str_replace('"', '""', $row['note']);        
        $rowObject = new \Magento\Framework\DataObject($row);
        $csv .= $rowObject->toString($template) . "\n";					      
      }
      
      return $csv;    
    }   

}