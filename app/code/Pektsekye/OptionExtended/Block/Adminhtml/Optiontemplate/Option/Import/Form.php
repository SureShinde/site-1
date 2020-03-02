<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Import;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

  protected function _prepareForm()
  {

      $form = $this->_formFactory->create(
          array('data' => array('id' => 'edit_form', 'action' => $this->getUrl('*/*/doimport', array('_current'=>true)), 'method' => 'post'))
      );
      
      $form->addField('product_id', 'hidden', array('name' => 'product_id'));
            
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }

 
}
