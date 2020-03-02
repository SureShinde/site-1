<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{


  protected function _prepareForm()
  {
        
      $form = $this->_formFactory->create();
      $fieldset = $form->addFieldset('optionextended_form', array('legend'=>__('General Information')));
     
      $fieldset->addField('title', 'text', array(
          'name'      => 'template_title',    
          'label'     => __('Template Name'),
          'required'  => true    
      ));

      $fieldset->addField('code', 'text', array(
          'name'      => 'template_code',
          'label'     => __('Code'),
          'required'  => true       
      ));
      
      $fieldset->addField('is_active', 'select', array(
          'name'      => 'is_active',
          'label'     => __('Status'),
          'options'   => array(
              1 => __('Enabled'),          
              0 => __('Disabled'))        
      ));
      
      $fieldset->addField('product_ids_string', 'hidden', array(
          'name'  => 'product_ids_string'
      ));
      
      $form->setValues($this->_coreRegistry->registry('current_template')->getData());
      $this->setForm($form);
           
      return parent::_prepareForm();      
  }

    
}
