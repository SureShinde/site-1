<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate;

class Value extends \Magento\Backend\Block\Widget\Grid\Container
{

  protected $_oxTemplate;
  protected $_oxTemplateOption;

  public function __construct(
      \Magento\Backend\Block\Widget\Context $context,
      \Pektsekye\OptionExtended\Model\Template $template,
      \Pektsekye\OptionExtended\Model\Template\Option $templateOption,      
      array $data = array()
  ) {
      $this->_oxTemplate = $template;
      $this->_oxTemplateOption = $templateOption;      
      parent::__construct($context, $data);
  }
  
  
  public function _construct()
  {
    $this->_controller = 'adminhtml_optiontemplate_value';
    $this->_blockGroup = 'Pektsekye_OptionExtended';
    
    $template = $this->_oxTemplate->load((int) $this->getRequest()->getParam('template_id'));
    $option = $this->_oxTemplateOption->load((int) $this->getRequest()->getParam('option_id'));
    $option->loadStoreFields(0);
    
    $this->_headerText = __('%1 - %2 - Values', $template->getTitle(), $option->getTitle());
    $this->addButtonLabel = __('Add Value');
    
    $this->addButton('back_templates_button', array(
        'label'   => __('Templates'),
        'onclick' => 'setLocation(\'' . $this->getBackTemplatesUrl() .'\')',
        'class'   => 'back'
    ));    
  
    $this->addButton('back_template_button', array(
        'label'   => __('Edit Template'),
        'onclick' => 'setLocation(\'' . $this->getBackTemplateUrl() .'\')',
        'class'   => 'back'
    ));
    
    $this->addButton('back_options_button', array(
        'label'   => __('Options'),
        'onclick' => 'setLocation(\'' . $this->getBackOptionsUrl() .'\')',
        'class'   => 'back'
    ));    
  
    $this->addButton('back_option_button', array(
        'label'   => __('Edit Option'),
        'onclick' => 'setLocation(\'' . $this->getBackOptionUrl() .'\')',
        'class'   => 'back'
    ));
    
    parent::_construct();
            
  }
  
  
  public function getBackOptionUrl()
  {
     return $this->getUrl('*/optiontemplate_option/edit', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));              
  } 
  
  public function getBackOptionsUrl()
  {
     return $this->getUrl('*/optiontemplate_option/index', array('template_id' => $this->getRequest()->getParam('template_id')));
  }     
  
  public function getBackTemplateUrl()
  {
     return $this->getUrl('*/optiontemplate/edit', array('template_id' => $this->getRequest()->getParam('template_id')));              
  } 

  public function getBackTemplatesUrl()
  {
      return $this->getUrl('*/optiontemplate/index');
  }   
        
  public function getCreateUrl()
  {
      return $this->getUrl('*/*/new', array('_current'=>true));
  }  
    
}
