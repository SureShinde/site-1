<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_oxTemplate;

    public function __construct(
        \Pektsekye\OptionExtended\Model\Template $template,    
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = array()
    ) {
        $this->_oxTemplate = $template;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('optionextendedgrid');
      //  $this->setRowClickCallback('bSelection.productGridRowClick.bind(bSelection)');
      //  $this->setCheckboxCheckCallback('bSelection.productGridCheckboxCheck.bind(bSelection)');
      //  $this->setRowInitCallback('bSelection.productGridRowInit.bind(bSelection)');
    //    $this->setDefaultSort('id');
        $this->setUseAjax(false);
    }


  protected function _prepareCollection()
  {
      $collection = $this->_oxTemplate->getCollection();      
      $this->setCollection($collection);      
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      $this->addColumn('title', array(
          'header'    => __('Template Name'),
          'align'     =>'left',
          'index'     => 'title'            
      ));
	  
      $this->addColumn('code', array(
          'header'    => __('Code'),
          'align'     =>'left',
          'width'     => 150,               	
          'index'     => 'code'          
      ));
      
      $this->addColumn('product_ids', array(
          'header'     => __('Products'),
          'align'      =>'left',
          'width'      => 150,  
          'html_decorators' => array('nobr') ,                     
          'filter'     => false,
          'sortable'   => false,
          'product_ids'=> $this->_oxTemplate->getResource()->getGridProductIds(),
          'renderer'   => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Grid\Renderer\Text'    
      )); 
           
      $this->addColumn('is_active', array(
          'header'    => __('Status'),
          'align'     =>'left', 	
          'width'     => 80,            
          'index'     => 'is_active',
          'type'      => 'options',
          'options'   => array(
              1 => __('Enabled'),          
              0 => __('Disabled'))          
      ));
      
        $this->addColumn('action',
            array(
                'header'      =>  __('Action'),
                'width'       => 150,                 
                'filter'      => false,
                'sortable'    => false,
                'renderer'    => 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Grid\Renderer\Action',
                'option_count'=> $this->_oxTemplate->getResource()->getGridOptionCount()                                
        ));

	  
      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
      $this->setMassactionIdField('template_id');
      $this->getMassactionBlock()->setFormFieldName('ids');

      $this->getMassactionBlock()->addItem('delete', array(
           'label'    => __('Delete'),
           'url'      => $this->getUrl('*/*/massDelete'),
           'confirm'  => __('Are you sure?')
      ));
      
      $this->getMassactionBlock()->addItem('status', array(
           'label'=> __('Change status'),
           'url'  => $this->getUrl('*/*/massStatus'),
           'additional' => array(
                  'visibility' => array(
                       'name'     => 'status',
                       'type'     => 'select',
                       'class'    => 'required-entry',
                       'label'    => __('Status'),
                       'values'   => array(
                                      1 => __('Enabled'),          
                                      0 => __('Disabled'))
                                     )
           )
      ));	
      return $this;
  }
  
  public function getGridUrl()
  {
      return $this->getUrl('*/*/index');
  }
    
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('template_id' => $row->getId()));
  }

}
