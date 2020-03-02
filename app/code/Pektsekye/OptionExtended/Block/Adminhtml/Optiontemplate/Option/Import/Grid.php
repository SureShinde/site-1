<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Import;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
  
  protected $_setsFactory;    
  protected $_productFactory;    
  protected $_type;      


  public function __construct( 
      \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,    
      \Magento\Catalog\Model\ProductFactory $productFactory,          
      \Magento\Catalog\Model\Product\Type $type,              
      \Magento\Backend\Block\Widget\Context $context,
      \Magento\Backend\Helper\Data $backendHelper,
      array $data = array()
  ) {
      $this->_setsFactory = $setsFactory;    
      $this->_productFactory = $productFactory;    
      $this->_type = $type;      
      parent::__construct($context, $backendHelper, $data);
  }


  public function _construct()
  {
      parent::_construct();
      $this->setId('optionextendedgrid');
      $this->setRowClickCallback('productGridRowClick');
      $this->setCheckboxCheckCallback('productGridCheckboxCheck');	        
      $this->setDefaultSort('product_id_filter');           
  }
	 
	 
	 
  protected function _prepareCollection()
  {
      $collection = $this->_productFactory->create()->getCollection()
          ->setStore($this->getStore())
          ->addAttributeToSelect('name')
          ->addAttributeToSelect('sku')
          ->addAttributeToSelect('price')
          ->addAttributeToSelect('attribute_set_id')
          ->addFieldToFilter('has_options',1);            

      $this->setCollection($collection);

      return parent::_prepareCollection();
  }


  protected function _prepareColumns()
  {
      $this->addColumn('in_products', array(
          'header_css_class' => 'a-center',
          'type'      => 'radio',
          'name'      => 'in_products',
          'align'     => 'center',
          'index'     => 'entity_id'
      ));
      
      $this->addColumn('product_id_filter',
          array(
              'header'=> __('ID'),
              'width' => '50px',
              'type'  => 'number',
              'index' => 'entity_id',
      )); 
    
      $this->addColumn('name', array(
          'header'    => __('Product Name'),
          'index'     => 'name',
          'column_css_class'=> 'name'
      ));

      $sets = $this->_setsFactory->create()->setEntityTypeFilter(
          $this->_productFactory->create()->getResource()->getTypeId()
      )->load()->toOptionHash();

      $this->addColumn(
          'set_name',
          array(
              'header' => __('Attribute Set'),
              'index' => 'attribute_set_id',
              'type' => 'options',
              'options' => $sets,
              'header_css_class' => 'col-attr-name',
              'column_css_class' => 'col-attr-name'
          )
      );

      $this->addColumn('sku', array(
          'header'    => __('SKU'),
          'width'     => '80px',
          'index'     => 'sku',
          'column_css_class'=> 'sku'
      ));
      
      $this->addColumn('type_id',
          array(
              'header'=> __('Type'),
              'width' => '60px',
              'index' => 'type_id',
              'type'  => 'options',
              'options' => $this->_type->getOptionArray(),
      ));

      return parent::_prepareColumns();
  }   


}
