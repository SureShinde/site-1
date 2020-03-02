<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

  protected $_oxTemplateOption;


  public function __construct(
      \Pektsekye\OptionExtended\Model\Template\Option $templateOption,    
      \Magento\Backend\Block\Widget\Context $context,
      \Magento\Backend\Helper\Data $backendHelper,
      array $data = array()
  ) {
      $this->_oxTemplateOption = $templateOption;
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
      $collection = $this->_oxTemplateOption->getCollection()
        ->joinTitle()
        ->joinPrice()         
        ->joinNote()                
        ->addFieldToFilter('template_id', (int) $this->getRequest()->getParam('template_id'));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      $this->addColumn('title', array(
          'header'    => __('Title'),
          'align'     =>'left',
          'index'     => 'title'                    
      ));
      
      $this->addColumn('code', array(
          'header'    => __('Code'),
          'align'     =>'left', 
          'width'     => '150',                      	
          'index'     => 'code'        
      ));

      
      $this->addColumn('type', array(
          'header'    => __('Type'),
          'align'     =>'left',
          'width'     => '110',           
          'html_decorators' => array('nobr') ,                         	
          'index'     => 'type',
          'type'      => 'options',
          'options'   => array(
                          "field" => __('Field'),
                          "area" => __('Area'),            
                          "file" => __('File'),            
                          "drop_down" => __('Drop-down'),
                          "radio" => __('Radio Buttons'),
                          "checkbox" => __('Checkbox'),
                          "multiple" => __('Multiple Select'),
                          "date" => __('Date'),
                          "date_time" => __('Date & Time'),
                          "time" => __('Time')
                         )                          
      ));
                      



      $this->addColumn('is_require', array(
          'header'    => __('Required'),
          'align'     =>'left',
          'width'     => '70',            	
          'index'     => 'is_require',
          'type'      => 'options',
          'options'   => array(
                          0 => __('No'),
                          1 => __('Yes')
                         )           
    
      ));

      
       $this->addColumn('sort_order', array(
          'header'    => __('Sort Order'),
          'align'     => 'left', 	
          'index'     => 'sort_order',
          'width'     => '60',                       
      ));   

        
      $this->addColumn('note', array(
          'header'    => __('Note'),
          'align'     => 'left', 
          'width'     => '150',            	
          'index'     => 'note',
          'html_decorators' => array('nobr') ,          
          'type'      => 'text',
          'truncate'  => 25,
          'escape'    => true                       
      ));
      
      $valueCount =  $this->_oxTemplateOption->getResource()->getGridValueCount((int) $this->getRequest()->getParam('template_id'));                                 
      
      $this->addColumn('action',
          array(
              'header'     =>  __('Values'),
              'width'      => '110',                               
              'filter'     => false,
              'sortable'   => false,
              'only_values'=> true,
              'renderer'   => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer\Action',
              'value_count'=> $valueCount
      ));
        
      $this->addColumn('layout', array(
          'header'    => __('Layout'),
          'align'     =>'left', 
          'width'     => '110',           
          'html_decorators' => array('nobr') ,           	
          'index'     => 'layout',
          'type'      => 'options',          
          'renderer'  => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer\Options',  
          'options'   => array(
                          'above'      =>__('Above Option'),        
                          'before'     =>__('Before Option'),
                          'below'      =>__('Below Option'),
                          'swap'       =>__('Main Image'),            
                          'grid'       =>__('Grid'),  
                          'gridcompact'=>__('Grid Compact'),                                
                          'list'       =>__('List'),  
                          'picker'     =>__('Color Picker'), 
                          'pickerswap' =>__('Picker & Main') 
                         )           
    
      ));

      $this->addColumn('popup', array(
          'header'    => __('Popup'),
          'align'     =>'left', 	
          'index'     => 'popup',
          'width'     => '60',            
          'type'      => 'options',            
          'renderer'  => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer\Options',          
          'options'   => array(
                          0 => __('No'),
                          1 => __('Yes')
                         )           
    
      ));
            
      $this->addColumn('selected_by_default', array(
          'header'    => __('Selected By Default'),
          'align'     =>'left', 	
          'index'     => 'selected_by_default',
          'width'     => '130',
          'type'      => 'text',
          'truncate'  => 20                               
      ));

      $this->addColumn('row_id', array(
          'header'    => __('Row Id'),
          'align'     =>'left', 	
          'index'     => 'row_id',
          'width'     => '60',                     
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
          'width'     => '90'          
      ));

      $this->addColumn('max_characters', array(
          'header'    => __('Max Characters'),
          'align'     =>'left',
          'index'     => 'max_characters',
          'width'     => '90'          
      ));

      $this->addColumn('file_extension', array(
          'header'    => __('File Extensions'),
          'align'     =>'left',
          'index'     => 'file_extension',
          'width'     => '90'          
      ));

      $this->addColumn('image_size_x', array(
          'header'    => __('Image Size X'),
          'align'     =>'left',
          'index'     => 'image_size_x',
          'width'     => '90'          
      ));

      $this->addColumn('image_size_y', array(
          'header'    => __('Image Size Y'),
          'align'     =>'left',
          'index'     => 'image_size_y',
          'width'     => '90'          
      ));                  

      
        $this->addColumn('action2',
            array(
                'header'    =>  __('Action'),
                'width'     => '150',                               
                'filter'    => false,
                'sortable'  => false,
                'renderer'   => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer\Action',
                'value_count'=> $valueCount
        ));
	  
      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
      $this->setMassactionIdField('option_id');
      $this->getMassactionBlock()->setFormFieldName('ids');

      $this->getMassactionBlock()->addItem('delete', array(
           'label'    => __('Delete'),
           'url'      => $this->getUrl('*/*/massDelete', array('template_id' => $this->getRequest()->getParam('template_id'))),
           'confirm'  => __('Are you sure?')
      ));
      
      return $this;
  }
  
  public function getGridUrl()
  {
      return $this->getUrl('optionextended/optiontemplate_option', array('template_id' => $this->getRequest()->getParam('template_id')));
  }
  
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('option_id' => $row->getId(), 'template_id' => $this->getRequest()->getParam('template_id')));
  }

}
