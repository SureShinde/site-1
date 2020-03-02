<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

  protected $_oxTemplateValue;

  public function __construct(
      \Pektsekye\OptionExtended\Model\Template\Value $templateValue,    
      \Magento\Backend\Block\Widget\Context $context,
      \Magento\Backend\Helper\Data $backendHelper,
      array $data = array()
  ) {
      $this->_oxTemplateValue = $templateValue;
      parent::__construct($context, $backendHelper, $data);
  }


  public function _construct()
  {
      parent::_construct();
      $this->setId('optionextendedgrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('asc');    
      $this->setUseAjax(false);
  }


  protected function _prepareCollection()
  {
      $collection = $this->_oxTemplateValue->getCollection()
      ->joinTitle(0)
      ->joinPrice(0)       
      ->joinDescription(0)             
      ->addFieldToFilter('option_id', (int) $this->getRequest()->getParam('option_id'));
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }


  protected function _afterLoadCollection()
  {    
      // to avoid ambigous "children" word used in Magento\Backend\Block\Widget\Grid\Extended
      foreach ($this->getCollection() as $item){
        $item->setChildrenIds($item->getChildren());
        $item->setChildren(null);
      }
 
      return parent::_afterLoadCollection();
  }


  protected function _prepareColumns()
  {
      $this->addColumn('row_id', array(
          'header'    => __('Row Id'),
          'align'     =>'left', 	
          'index'     => 'row_id',
          'width'     => '60'              
      ));     

      
      $this->addColumn('title', array(
          'header'    => __('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
    
      $this->addColumn('price', array(
          'header'    => __('Price'),
          'align'     =>'left', 	
          'index'     => 'price',
          'width'     => '50'                     
      ));
      
      $this->addColumn('price_type', array(
          'header'    => __('Price Type'),
          'align'     =>'left',
          'width'     => '70',             	
          'index'     => 'price_type',
          'type'      => 'options',
          'options'   => array(
                           'fixed'   => __('Fixed'),
                           'percent' => __('Percent')
                         )                   
      ));
        
      $this->addColumn('sku', array(
          'header'    => __('Sku'),
          'align'     =>'left',
          'index'     => 'sku',
          'width'     => '140'         
      ));

      $this->addColumn('children_ids', array(
          'header'    => __('Children'),
          'align'     =>'left', 	
          'width'     => '150',
          'html_decorators' => array('nobr') ,                         
          'index'     => 'children_ids',
          'type'      => 'text',
          'truncate'  => 25         
                   
      ));
   
      $this->addColumn('image', array(
          'header'    => __('Image'),
          'align'     =>'center', 	
          'width'     => '45',           
          'index'     => 'image',
          'renderer'   => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Grid\Renderer\Image'                     
      ));      

    
      $this->addColumn('description', array(
          'header'    => __('Description'),
          'align'     =>'left',
          'width'     => '150',  
          'html_decorators' => array('nobr') ,                         	
          'index'     => 'description',
          'type'      => 'text',
          'truncate'  => 25,
          'escape'    => true                     
      ));

      $this->addColumn('sort_order', array(
          'header'    => __('Sort Order'),
          'align'     => 'left', 
          'width'     => '60',          	
          'index'     => 'sort_order'
      ));
     
        
        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'width'     => '40',               
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('Edit'),
                        'url'       => array('base'=> '*/*/edit','params'=>array('value_id' => $this->getRequest()->getParam('value_id'),'option_id' => $this->getRequest()->getParam('option_id'), 'template_id' => $this->getRequest()->getParam('template_id'))),
                        'field'     => 'value_id'
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
      $this->setMassactionIdField('value_id');
      $this->getMassactionBlock()->setFormFieldName('ids');

      $this->getMassactionBlock()->addItem('delete', array(
           'label'    => __('Delete'),
           'url'      => $this->getUrl('*/*/massDelete', array('value_id' => $this->getRequest()->getParam('value_id'), 'option_id' => $this->getRequest()->getParam('option_id'), 'template_id' => $this->getRequest()->getParam('template_id'))),
           'confirm'  => __('Are you sure?')
      ));
	
      return $this;
  }
  
  public function getGridUrl()
  {
      return $this->getUrl('optionextended/optiontemplate_value', array('option_id' => $this->getRequest()->getParam('option_id'), 'template_id' => $this->getRequest()->getParam('template_id')));
  }
  
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('value_id' => $row->getId(), 'option_id' => $this->getRequest()->getParam('option_id'), 'template_id' => $this->getRequest()->getParam('template_id')));
  }
  

  
}
