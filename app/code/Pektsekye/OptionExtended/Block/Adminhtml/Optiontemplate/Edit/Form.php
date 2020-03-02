<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

class Form extends Generic
{
  
  protected function _prepareForm()
  {
      /** @var DataForm $form */
      $form = $this->_formFactory->create(
          array('data' => array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save', array('_current'=>true)), 'method' => 'post'))
      );
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }  
 
}
