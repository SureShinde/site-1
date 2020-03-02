<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel;

class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {    
      $this->_init('optionextended_value', 'ox_value_id');
    }  
 

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {

      $descriptionTable = $this->getTable('optionextended_value_description');
        
      if (!$object->getData('scope', 'optionextended_description')) {		
        $statement = $this->getConnection()->select()
          ->from($descriptionTable)
          ->where('ox_value_id = '.$object->getId().' AND store_id = ?', 0);

        if ($this->getConnection()->fetchOne($statement)) {
          if ($object->getStoreId() == '0') {
            $this->getConnection()->update(
              $descriptionTable,
                array('description' => $object->getDescription()),
                $this->getConnection()->quoteInto('ox_value_id='.$object->getId().' AND store_id=?', 0)
            );
          }
        } else {
          $this->getConnection()->insert(
            $descriptionTable,
              array(
                'ox_value_id' => $object->getId(),
                'store_id' => 0,
                'description' => $object->getDescription()
          ));
        }
      }
    
      if ($object->getStoreId() != '0' && !$object->getData('scope', 'optionextended_description')) {
        $statement = $this->getConnection()->select()
          ->from($descriptionTable)
          ->where('ox_value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

        if ($this->getConnection()->fetchOne($statement)) {;
          $this->getConnection()->update(
            $descriptionTable,
              array('description' => $object->getDescription()),
              $this->getConnection()->quoteInto('ox_value_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
        } else {
          $this->getConnection()->insert(
            $descriptionTable,
              array(
                'ox_value_id' => $object->getId(),
                'store_id' => $object->getStoreId(),
                'description' => $object->getDescription()
          ));
        }
      } elseif ($object->getData('scope', 'optionextended_description')){
          $this->getConnection()->delete(
              $descriptionTable,
              $this->getConnection()->quoteInto('ox_value_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
          );		    
      }
    }


     public function duplicate($oldOptionId, $newOptionId, $newProductId)
    {

      $productOptionValueTable = $this->getTable('catalog_product_option_type_value');
      $descriptionTable = $this->getTable('optionextended_value_description');				
        
      $select = $this->getConnection()->select()
        ->from($productOptionValueTable, 'option_type_id')
        ->where('option_id=?', $oldOptionId);
      $oldTypeIds = $this->getConnection()->fetchCol($select);

      $select = $this->getConnection()->select()
        ->from($productOptionValueTable, 'option_type_id')
        ->where('option_id=?', $newOptionId);
      $newTypeIds = $this->getConnection()->fetchCol($select);

      foreach ($oldTypeIds as $ind => $oldTypeId) {
      
      // read and prepare original optionextended values
        $select = $this->getConnection()->select()
          ->from($this->getMainTable())
          ->where('option_type_id=?', $oldTypeId);
        $row = $this->getConnection()->fetchRow($select);
        $oldOxValueId = $row['ox_value_id'];
        $row['option_type_id'] = $newTypeIds[$ind];						
        $row['product_id'] = $newProductId;				
        unset($row['ox_value_id']);

      // insert optionextended values to duplicated option values
        $this->getConnection()->insert($this->getMainTable(), $row);
        $newOxValueId = $this->getConnection()->lastInsertId();

      // copy optionextended values note
        $sql = 'REPLACE INTO `' . $descriptionTable . '` '
           . 'SELECT NULL, ' . $newOxValueId . ', `store_id`, `description`'
           . 'FROM `' . $descriptionTable . '` WHERE `ox_value_id`=' . $oldOxValueId;
        $this->getConnection()->query($sql);
          
      }
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
        SELECT oxo.code as option_code,oxv.row_id,pott.title,potp.price,potp.price_type,potv.sku,potv.sort_order,oxv.children,oxv.image,oxvd.description  
        FROM `{$this->getTable('catalog_product_option')}` po 
        JOIN `{$this->getTable('optionextended_option')}` oxo 
          ON oxo.option_id = po.option_id 
        JOIN `{$this->getTable('catalog_product_option_type_value')}` potv 
          ON potv.option_id = po.option_id
        JOIN `{$this->getTable('optionextended_value')}` oxv 
          ON oxv.option_type_id = potv.option_type_id        
        LEFT JOIN  `{$this->getTable('catalog_product_option_type_title')}` pott 
          ON pott.option_type_id = potv.option_type_id AND pott.store_id = 0
        LEFT JOIN  `{$this->getTable('catalog_product_option_type_price')}` potp 
          ON potp.option_type_id = potv.option_type_id AND potp.store_id = 0
        LEFT JOIN  `{$this->getTable('optionextended_value_description')}` oxvd 
          ON oxvd.ox_value_id = oxv.ox_value_id AND oxvd.store_id = 0                                                                                          
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
        SELECT oxo.code as option_code,oxv.row_id,cs.code as store,pott.title,oxvd.description  
        FROM `{$this->getTable('store')}` cs
        JOIN `{$this->getTable('optionextended_option')}` oxo
        JOIN `{$this->getTable('catalog_product_option_type_value')}` potv 
          ON potv.option_id = oxo.option_id                  
        JOIN `{$this->getTable('optionextended_value')}` oxv 
          ON oxv.option_type_id = potv.option_type_id                  
        LEFT JOIN  `{$this->getTable('catalog_product_option_type_title')}` pott 
          ON pott.option_type_id = potv.option_type_id AND pott.store_id = cs.store_id
        LEFT JOIN  `{$this->getTable('optionextended_value_description')}` oxvd 
          ON oxvd.ox_value_id = oxv.ox_value_id AND oxvd.store_id = cs.store_id
        ORDER BY oxo.code,oxv.row_id,cs.code                                                                                         
      ");

      while ($row = $data->fetch()){    
        $row['description'] = str_replace('"', '""', $row['description']);       
        $rowObject = new \Magento\Framework\DataObject($row);
        $csv .= $rowObject->toString($template) . "\n";					      
      }
      
      return $csv;    
    }  	
	   
  
	
}