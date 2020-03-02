<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit\Tab\Products;

use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{


    protected $_productIds = array(); 
    
    protected $_setsFactory;    
    protected $_productFactory;    
    protected $_type;      
    protected $_coreRegistry = null;


    public function __construct( 
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,    
        \Magento\Catalog\Model\ProductFactory $productFactory,          
        \Magento\Catalog\Model\Product\Type $type,   
        \Magento\Framework\Registry $registry,             
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = array()
    ) {
        $this->_setsFactory = $setsFactory;    
        $this->_productFactory = $productFactory;    
        $this->_type = $type;    
        $this->_coreRegistry = $registry;    
        parent::__construct($context, $backendHelper, $data);
    }


    public function _construct()
    {
        parent::_construct();
        $this->setId('optionextended_edit_tab_products_grid');	              		  
        $this->setDefaultSort('product_id_filter');
        $products = $this->getSelectedProducts();
        if (!empty($products)){
          $this->setDefaultFilter(array('massaction'=>1));      
        }  
        $this->setUseAjax(true);   
    }
	
	
    protected function _addColumnFilterToCollection($column)
    {

        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

	 
    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create()->getCollection()
            ->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        
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
                'header'  => __('Type'),
                'index'   => 'type_id',
                'type'    => 'options',
                'options' => $this->_type->getOptionArray(),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type'
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'width'     => '40',               
                'type'      => 'action',
                'getter'    => 'getId',                
                'actions'   => array(
                    array(
                        'caption'   => __('View'),
                        'url'       => array('base'=> 'catalog/product/edit', 'params' => array('active_tab'=>'customer_options')),
                        'field'     => 'id',
                        'target'=>	'_blank'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));
        
        return parent::_prepareColumns();
    }   


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->addItem('apply', array());

        $this->setAdditionalJavaScript('
          '.$this->getMassactionBlock()->getJsObjectName().'._updateCount = '.$this->getMassactionBlock()->getJsObjectName().'.updateCount;
          '.$this->getMassactionBlock()->getJsObjectName().'.updateCount = function() {
              this._updateCount();
              $("product_ids_string").value = this.checkedString;                     
            }; 
          '.$this->getMassactionBlock()->getJsObjectName().'.grid.rowClickCallback = function(grid, evt) {
              this.onGridRowClick(grid, evt);
              var tdElement = Event.findElement(evt, "td");
              if (!$(tdElement).down("input")) {         
                var checkbox = this.findCheckbox(evt);
                if (checkbox) {
                  checkbox.checked = !checkbox.checked;
                  this.setCheckbox(checkbox);
                }   
              }                              
            }.bind('.$this->getMassactionBlock()->getJsObjectName().');                              
          '.$this->getMassactionBlock()->getJsObjectName().'.setOldCallback("init", function(){

              $("'.$this->getMassactionBlock()->getHtmlId().'-form").remove();
            });
          '.$this->getMassactionBlock()->getJsObjectName().'.getOldCallback("init")();
        ');

        $selected = $this->_coreRegistry->registry('current_template')->getProductIdsString();
        $this->getRequest()->setPostValue('internal_ids', $selected);

        return $this;
    } 

    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array('_current'=>true));
    }  


    protected function getSelectedProducts()
    {
        if ($products = $this->getRequest()->getPost('products', null)) {
            return $products;
        } else {
            return $this->_coreRegistry->registry('current_template')->getProductIds();
        }
    }


}

