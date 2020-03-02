<?php

namespace Pektsekye\OptionExtended\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        

        $installer->getConnection()->dropTable($installer->getTable('optionextended_option'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_option'))
            ->addColumn('ox_option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended Option Id')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Option Id')      
            ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Product Id')  
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64', array(
                'nullable'  => false  
                ), 'Code')         
            ->addColumn('row_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => true      
                ), 'Row Id')         
            ->addColumn('layout', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(   
                ), 'Layout')   
            ->addColumn('popup', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,        
                'default'   => '0',
                ), 'Popup')
            ->addColumn('selected_by_default', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'Selected By Default')                   
            ->addIndex($installer->getIdxName('optionextended_option', array('option_id'), true),
                array('option_id'), array('type' => 'unique')) 
            ->addIndex($installer->getIdxName('optionextended_option', array('code'), true),
                array('code'), array('type' => 'unique'))                
            ->addIndex($installer->getIdxName('optionextended_option', array('product_id')), array('product_id'))
            ->addForeignKey(
                $installer->getFkName('optionextended_option', 'option_id', 'catalog_product_option', 'option_id'),
                'option_id', $installer->getTable('catalog_product_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)       
            ->setComment('OptionExtended Option');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_option_note'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_option_note'))
            ->addColumn('ox_option_note_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended Option Note Id')
            ->addColumn('ox_option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'OptionExtended Option Id')      
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0'
                ), 'Store Id')  
            ->addColumn('note', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                'nullable'  => false        
                ), 'Note')                        
            ->addIndex($installer->getIdxName('optionextended_option_note', array('ox_option_id', 'store_id'),\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
             array('ox_option_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))                     
            ->addIndex($installer->getIdxName('optionextended_option_note', array('ox_option_id')), array('ox_option_id'))
            ->addIndex($installer->getIdxName('optionextended_option_note', array('store_id')), array('store_id'))    
            ->addForeignKey(
                $installer->getFkName('optionextended_option_note', 'ox_option_id', 'optionextended_option', 'ox_option_id'),
                'ox_option_id', $installer->getTable('optionextended_option'), 'ox_option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)  
            ->addForeignKey($installer->getFkName('optionextended_option_note', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)             
            ->setComment('OptionExtended Option Note');
        $installer->getConnection()->createTable($table);



        $installer->getConnection()->dropTable($installer->getTable('optionextended_value'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_value'))
            ->addColumn('ox_value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended Value Id')
            ->addColumn('option_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Option Type Id')      
            ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Product Id')  
            ->addColumn('row_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => true,
                'default'   => null        
                ), 'Row Id')  
            ->addColumn('children', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                'nullable'  => false       
                ), 'Children')  
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', array(
                'nullable'  => false      
                ), 'Image')                                 
            ->addIndex($installer->getIdxName('optionextended_value', array('option_type_id'), true),
                array('option_type_id'), array('type' => 'unique'))        
            ->addIndex($installer->getIdxName('optionextended_value', array('product_id')), array('product_id'))
            ->addForeignKey(
                $installer->getFkName('optionextended_value', 'option_type_id', 'catalog_product_option_type_value', 'option_type_id'),
                'option_type_id', $installer->getTable('catalog_product_option_type_value'), 'option_type_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)       
            ->setComment('OptionExtended Value');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_value_description'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_value_description'))
            ->addColumn('ox_value_description_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended Value Description Id')
            ->addColumn('ox_value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'OptionExtended Value Id')      
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0'
                ), 'Store Id')  
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                'nullable'  => false       
                ), 'Description')                        
            ->addIndex($installer->getIdxName('optionextended_value_description', array('ox_value_id', 'store_id'),\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
             array('ox_value_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))                    
            ->addIndex($installer->getIdxName('optionextended_value_description', array('ox_value_id')), array('ox_value_id'))
            ->addIndex($installer->getIdxName('optionextended_value_description', array('store_id')), array('store_id'))    
            ->addForeignKey(
                $installer->getFkName('optionextended_value_description', 'ox_value_id', 'optionextended_value', 'ox_value_id'),
                'ox_value_id', $installer->getTable('optionextended_value'), 'ox_value_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)  
            ->addForeignKey($installer->getFkName('optionextended_value_description', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)             
            ->setComment('OptionExtended Value Description');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_template'))
            ->addColumn('template_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended Template Id')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', array(
                'nullable'  => false       
                ), 'Title')     
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64', array(
                'nullable'  => false       
                ), 'Code')                   
            ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1'
                ), 'Status')                       
            ->addIndex($installer->getIdxName('optionextended_template', array('code'),\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
             array('code'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))                                
            ->setComment('OptionExtended Template');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_option'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_template_option'))
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'Template Option Id')
            ->addColumn('template_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Template Id')      
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64', array(
                'nullable'  => false  
                ), 'Code')         
            ->addColumn('row_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => true      
                ), 'Row Id')  
            ->addColumn('type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, array(
               'nullable'   => true, 
               'default'    => null
                ), 'Type')
            ->addColumn('is_require', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'nullable'  => false, 
                'default'   => '1'
                ), 'Is Required')
            ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, array(
                ), 'SKU')
            ->addColumn('max_characters', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true
                ), 'Max Characters')
            ->addColumn('file_extension', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, array(
                ), 'File Extension')
            ->addColumn('image_size_x', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true
                ), 'Image Size X')
            ->addColumn('image_size_y', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true
                ), 'Image Size Y')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true, 
                'nullable'  => false, 
                'default'   => '0'
                ),'Sort Order')                
            ->addColumn('layout', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(   
                ), 'Layout')   
            ->addColumn('popup', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,        
                'default'   => '0',
                ), 'Popup')
            ->addColumn('selected_by_default', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'Selected By Default')                   
            ->addIndex($installer->getIdxName('optionextended_template_option', array('template_id')), array('template_id')) 
            ->addIndex($installer->getIdxName('optionextended_template_option', array('code'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('code'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))                
            ->addForeignKey($installer->getFkName('optionextended_template_option', 'template_id', 'optionextended_template', 'template_id'),
                'template_id', $installer->getTable('optionextended_template'), 'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setOption('auto_increment', 1000000)               
            ->setComment('OptionExtended Template Option');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_option_title'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_option_title'))
            ->addColumn('option_title_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary' => true
                ), 'Option Title ID')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Store ID')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable' => true, 
                'default' => null
                ), 'Title')
            ->addIndex($installer->getIdxName('optionextended_template_option_title', array('option_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('option_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_option_title', array('option_id')),
                array('option_id'))
            ->addIndex($installer->getIdxName('optionextended_template_option_title', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_option_title', 'option_id', 'optionextended_template_option', 'option_id'),
                'option_id', $installer->getTable('optionextended_template_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_option_title', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Option Title');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_option_price'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_option_price'))
            ->addColumn('option_price_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary'  => true
                ), 'Option Price ID')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default'  => '0'
                ), 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default'  => '0'
                ), 'Store ID')
            ->addColumn('price', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
                'nullable' => false, 
                'default'  => '0.0000'
                ), 'Price')
            ->addColumn('price_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 7, array(
                'nullable' => false, 
                'default'  => 'fixed'
                ), 'Price Type')
            ->addIndex($installer->getIdxName('optionextended_template_option_price', array('option_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('option_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_option_price', array('option_id')),
                array('option_id'))
            ->addIndex($installer->getIdxName('optionextended_template_option_price', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_option_price', 'option_id', 'optionextended_template_option', 'option_id'),
                'option_id', $installer->getTable('optionextended_template_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_option_price', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Option Price');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_option_note'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_option_note'))
            ->addColumn('option_note_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary' => true
                ), 'Option Note ID')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Store ID')
            ->addColumn('note', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                'nullable' => true, 
                'default' => null
                ), 'Note')
            ->addIndex($installer->getIdxName('optionextended_template_option_note', array('option_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('option_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_option_note', array('option_id')),
                array('option_id'))
            ->addIndex($installer->getIdxName('optionextended_template_option_note', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_option_note', 'option_id', 'optionextended_template_option', 'option_id'),
                'option_id', $installer->getTable('optionextended_template_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_option_note', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Option Note');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_value'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_value'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,    
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'Template Value Id')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Template Option Id')             
            ->addColumn('row_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false      
                ), 'Row Id')  
            ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, array(
                ), 'SKU')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true, 
                'nullable'  => false, 
                'default'   => '0'
                ),'Sort Order') 
            ->addColumn('children', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                ), 'Children')
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'Image')                                                
            ->addIndex($installer->getIdxName('optionextended_template_value', array('option_id')), array('option_id'))               
            ->addForeignKey($installer->getFkName('optionextended_template_value', 'option_id', 'optionextended_template_option', 'option_id'),
                'option_id', $installer->getTable('optionextended_template_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setOption('auto_increment', 1000000)               
            ->setComment('OptionExtended Template Value');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_value_title'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_value_title'))
            ->addColumn('value_title_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary' => true
                ), 'Value Title ID')
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Value ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Store ID')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable' => true, 
                'default' => null
                ), 'Title')
            ->addIndex($installer->getIdxName('optionextended_template_value_title', array('value_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('value_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_value_title', array('value_id')),
                array('value_id'))
            ->addIndex($installer->getIdxName('optionextended_template_value_title', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_value_title', 'value_id', 'optionextended_template_value', 'value_id'),
                'value_id', $installer->getTable('optionextended_template_value'), 'value_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_value_title', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Value Title');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_value_price'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_value_price'))
            ->addColumn('value_price_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary'  => true
                ), 'Value Price ID')
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default'  => '0'
                ), 'Value ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default'  => '0'
                ), 'Store ID')
            ->addColumn('price', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
                'nullable' => false, 
                'default'  => '0.0000'
                ), 'Price')
            ->addColumn('price_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 7, array(
                'nullable' => false, 
                'default'  => 'fixed'
                ), 'Price Type')
            ->addIndex($installer->getIdxName('optionextended_template_value_price', array('value_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('value_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_value_price', array('value_id')),
                array('value_id'))
            ->addIndex($installer->getIdxName('optionextended_template_value_price', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_value_price', 'value_id', 'optionextended_template_value', 'value_id'),
                'value_id', $installer->getTable('optionextended_template_value'), 'value_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_value_price', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Value Price');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_template_value_description'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_template_value_description'))
            ->addColumn('value_description_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity' => true, 
                'unsigned' => true, 
                'nullable' => false, 
                'primary' => true
                ), 'Value Title ID')
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Value ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                'unsigned' => true, 
                'nullable' => false, 
                'default' => '0'
                ), 'Store ID')
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(
                'nullable' => true, 
                'default' => null
                ), 'Description')
            ->addIndex($installer->getIdxName('optionextended_template_value_description', array('value_id', 'store_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('value_id', 'store_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
            ->addIndex($installer->getIdxName('optionextended_template_value_description', array('value_id')),
                array('value_id'))
            ->addIndex($installer->getIdxName('optionextended_template_value_description', array('store_id')),
                array('store_id'))
            ->addForeignKey($installer->getFkName('optionextended_template_value_description', 'value_id', 'optionextended_template_value', 'value_id'),
                'value_id', $installer->getTable('optionextended_template_value'), 'value_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('optionextended_template_value_description', 'store_id', 'store', 'store_id'), 
                'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('OptionExtended Template Value Description');
        $installer->getConnection()->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('optionextended_product_template'));
        $table = $installer->getConnection()->newTable($installer->getTable('optionextended_product_template'))      
            ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Product Id')  
            ->addColumn('template_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false
                ), 'Template Id')                          
            ->addIndex($installer->getIdxName('optionextended_product_template', array('product_id', 'template_id'), \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                array('product_id', 'template_id'), array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))          
            ->addForeignKey(
                $installer->getFkName('optionextended_product_template', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)             
            ->addForeignKey($installer->getFkName('optionextended_product_template', 'template_id', 'optionextended_template', 'template_id'),
                'template_id', $installer->getTable('optionextended_template'), 'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)      
            ->setComment('OptionExtended Product Template');
        $installer->getConnection()->createTable($table);

   
        $installer->getConnection()->dropTable($installer->getTable('optionextended_pickerimage'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('optionextended_pickerimage'))   
            ->addColumn('ox_image_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'identity'  => true,        
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true
                ), 'OptionExtended PickerImages Image ID')           
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'Title')  
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                ), 'Image')                         
            ->setComment('OptionExtended PickerImages Image Table');
        $installer->getConnection()->createTable($table);   
   
   
        $setup->endSetup();

    }
}
