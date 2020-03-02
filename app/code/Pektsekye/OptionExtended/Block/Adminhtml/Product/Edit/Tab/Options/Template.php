<?php


namespace Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options;

use Magento\Backend\Block\Widget;

class Template extends Widget
{  

    protected $_template = 'product/edit/options/template.phtml';
    
    protected $_appliedTemplates;
    protected $_oxTemplate;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Pektsekye\OptionExtended\Model\ResourceModel\Template $oxTemplate,
        array $data = array()
    ) {
        $this->_oxTemplate = $oxTemplate;
        parent::__construct($context, $data);
    }
    
    
    protected function _prepareLayout()
    {
        
        $this->addChild(
            'delete_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Remove'), 'class' => 'delete')
        );        

        $this->addChild(
            'apply_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Apply'), 'onclick' => 'optionExtended.applyTemplate()')
        ); 
 
        $this->addChild(
            'use_template_options_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Insert'), 'onclick' => 'optionExtended.insertTemplateOptions()')
        ); 
             
        return parent::_prepareLayout();
    }



    public function getDeleteButtonHtml($templateId)
    {
        $this->getChild('delete_button')->setData('onclick', 'optionExtended.removeTemplate('.$templateId.')');
        return $this->getChildHtml('delete_button');          
    }	
    
    public function getApplyButtonHtml()
    {
        return $this->getChildHtml('apply_button');
    }

    public function getUseTemplateOptionsButtonHtml()
    {
        return $this->getChildHtml('use_template_options_button');
    }

 

    public function getTemplateSelect()
    {          
        $html = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')
            ->setName('ox_template_select')
            ->setId('ox_template_select')
            ->setClass('optionextended-template-select')
            ->setOptions($this->_oxTemplate->getTemplatesAsOptionArray((int) $this->getProductId()))
            ->setExtraParams('data-form-part="product_form"')
            ->getHtml();

        return $html;                     
    }


    public function getAppliedTemplates()
    {
      if (!isset($this->_appliedTemplates))
        $this->_appliedTemplates = $this->_oxTemplate->getAppliedTemplates((int) $this->getProductId()); 
        
      return $this->_appliedTemplates;   
    }

    
    public function getOptionsUrl($templateId = null)
    {   
        if ($templateId)
          return $this->getUrl('optionextended/optiontemplate_option/index', array('template_id' => (int) $templateId));
        else          
          return $this->getUrl('optionextended/optiontemplate_option/index');                          
    }


    public function getTemplateIdsString()
    {
      $idsString = '';
      foreach ($this->getAppliedTemplates() as $row)
        $idsString .= ($idsString != '' ? ',' : '') . $row['template_id'];
      return $idsString;    
    }    
        
}
