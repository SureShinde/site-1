<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    

    public function _construct()
    {
        $this->_objectId   = 'option_id';
        $this->_blockGroup = 'Pektsekye_OptionExtended';        
        $this->_controller = 'adminhtml_optiontemplate_option';
 
        
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
            'onclick' => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'   => 'back'
        ));  
        
        parent::_construct();
        
        $this->removeButton('back');         
        $this->removeButton('reset'); 
        
        if (!is_null($this->_coreRegistry->registry('current_option')->getId())) {                
          $this->addButton('dulicate_button', array(
              'label'   => __('Duplicate'),
              'onclick' => 'setLocation(\'' . $this->getDuplicateUrl() .'\')',
              'class'   => 'add'
          ));
        }                      
        
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
        

                
        $this->_formScripts[] = '
                optionExtended.aboveOption      = "'. __('Above Option') .'";
                optionExtended.beforeOption     = "'. __('Before Option') .'";
                optionExtended.belowOption      = "'. __('Below Option') .'";
                optionExtended.grid             = "'. __('Grid') .'";
                optionExtended.gridcompact      = "'. __('Grid Compact') .'";                
                optionExtended.list             = "'. __('List') .'";
                optionExtended.mainImage        = "'. __('Main Image') .'";
                optionExtended.colorPicker      = "'. __('Color Picker') .'";
                optionExtended.colorPickerSwap  = "'. __('Picker & Main') .'";
             ';  
 /*            
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";                   
    */
    }


    public function getHeaderText()
    {
        if (!is_null($this->_coreRegistry->registry('current_option')->getId())) {
            return $this->_coreRegistry->registry('current_option')->getTitle();
        } else {
            return __('Add Option');
        }
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current' => true));
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('template_id' => $this->getRequest()->getParam('template_id')));
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
