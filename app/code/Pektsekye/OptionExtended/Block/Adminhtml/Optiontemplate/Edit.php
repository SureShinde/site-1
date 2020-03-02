<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate;


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
        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'Pektsekye_OptionExtended';        
        $this->_controller = 'adminhtml_optiontemplate';

        
        parent::_construct();
        
        $this->updateButton('back', 'label', __('Templates'));
          
        if (!is_null($this->_coreRegistry->registry('current_template')->getId())) {               
           $this->addButton('dulicate_button', array(
              'label'   => __('Duplicate'),
              'onclick' => 'setLocation(\'' . $this->getDuplicateUrl() .'\')',
              'class'   => 'add'
           ));
        }
              
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
        if (!is_null($this->_coreRegistry->registry('current_template')->getId())) {
            return $this->_coreRegistry->registry('current_template')->getTitle();
        } else {
            return __('Add Template');
        }
    }
  

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current' => true));
    }
    
 /*   
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('* /* /save', array('_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}'));
    }    
   */ 
}
