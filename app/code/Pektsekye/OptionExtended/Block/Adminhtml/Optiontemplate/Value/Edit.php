<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_oxTemplateOption;
    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Pektsekye\OptionExtended\Model\Template $templateOption,        
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_oxTemplateOption = $templateOption;    
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    

    public function _construct()
    {
        $this->_objectId   = 'value_id';
        $this->_blockGroup = 'Pektsekye_OptionExtended';        
        $this->_controller = 'adminhtml_optiontemplate_value';
 
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
        
        $this->addButton('back_values_button', array(
            'label'   => __('Values'),
            'onclick' => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'   => 'back'
        ));  
        
        parent::_construct();
        
        $this->removeButton('back');        
        $this->removeButton('reset'); 

        $this->addButton(
            'save_and_continue',
            array(
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form')
                    )
                )
            ),
            10
        );      
    
    }


    public function getHeaderText()
    {
        if ( $this->_coreRegistry->registry('current_value') && $this->_coreRegistry->registry('current_value')->getId() ) {
            return $this->_coreRegistry->registry('current_value')->getTitle();
        } else {
            $storeId = (int) $this->getRequest()->getParam('store');    
            $optionTitle = $this->_oxTemplateOption->load((int) $this->getRequest()->getParam('option_id'))
              ->loadStoreFields($storeId)
              ->getTitle();         
            return __('Add Value of %1', $optionTitle);
        }
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('template_id' => $this->getRequest()->getParam('template_id'), 'option_id' => $this->getRequest()->getParam('option_id')));
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
        
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }     


}
