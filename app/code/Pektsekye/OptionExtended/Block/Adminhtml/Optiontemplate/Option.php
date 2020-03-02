<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate;

class Option extends \Magento\Backend\Block\Widget\Grid\Container
{


  protected $_oxTemplate;


  public function __construct(
      \Magento\Backend\Block\Widget\Context $context,
      \Pektsekye\OptionExtended\Model\Template $template,
      array $data = array()
  ) {
      $this->_oxTemplate = $template;
      parent::__construct($context, $data);
  }



  protected function _construct()
  {  
    $this->_controller = 'adminhtml_optiontemplate_option';
    $this->_blockGroup = 'Pektsekye_OptionExtended';

    $template = $this->_oxTemplate->load((int) $this->getRequest()->getParam('template_id'));

  //  $this->_headerText = __('%1 - Options', $template->getTitle());
    $this->addButtonLabel = __('Add Option');

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
    
    if (!$template->getHasOptions()){    
      $this->addButton('import_button', array(
          'label'   => __('Import Options From Product'),
          'onclick' => 'setLocation(\'' . $this->getImportUrl() .'\')',
          'class'   => 'add'
      ));    
    }   
         
    parent::_construct();            
  }
  
  
  public function getImportUrl()
  {
      return $this->getUrl('*/optiontemplate_option/import', array('template_id' => $this->getRequest()->getParam('template_id')));
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
